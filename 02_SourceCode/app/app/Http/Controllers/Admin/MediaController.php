<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\MediaStoreRequest;
use App\Http\Resources\MediaItemResource;
use App\Models\MediaAsset;
use App\Models\TourRoute;
use App\Services\AuditLogger;
use App\Services\SearchService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Inertia\Inertia;
use Inertia\Response;

class MediaController extends Controller
{
    public const SUBJECTS = SearchService::TYPES + ['route' => TourRoute::class];
    public const SUBJECT_ROUTES = ['site' => ['sites.show', 'site'], 'artifact' => ['artifacts.show', 'artifact'],
        'figure' => ['figures.show', 'figure'], 'route' => ['tours.show', 'route']];

    public function index(Request $request): Response
    {
        $filters = $this->filters($request, ['type', 'license', 'q']);
        $media = MediaAsset::query()->with(['captions', 'subject'])
            ->when($filters['type'], fn ($q, $v) => $q->where('media_type', $v))
            ->when($filters['license'], fn ($q, $v) => $q->where('license', $v))
            ->when($filters['q'], fn ($q, $v) => $q->where(fn ($w) => $w->where('source_name', 'like', '%'.str_replace(['%', '_'], ['\\%', '\\_'], $v).'%')
                ->orWhere('path', 'like', '%'.str_replace(['%', '_'], ['\\%', '\\_'], $v).'%')))
            ->latest('id')->paginate(24)->withQueryString()
            ->through(fn (MediaAsset $m) => MediaItemResource::one($m) + ['subject' => $this->subject($m)]);

        return Inertia::render('Admin/Media/Index', [
            'media' => $media->toArray(),
            'filters' => $filters,
            'enums' => HeritagePresenter::commonEnums() + [
                'media_types' => HeritagePresenter::enumList('media_types', ['image', 'audio', 'model3d']),
                'subject_types' => array_keys(self::SUBJECTS),
            ],
        ]);
    }

    public function store(MediaStoreRequest $request, AuditLogger $audit): RedirectResponse
    {
        $data = $request->validated();
        $subject = self::SUBJECTS[$data['subject_type']]::query()->findOrFail($data['subject_id']);
        $file = $request->file('file');

        $media = DB::transaction(function () use ($data, $subject, $file, $request) {
            $path = $file?->storeAs('media/'.$data['media_type'], Str::slug($subject->slug).'-'.Str::lower(Str::random(8)).'.'.$file->extension(), 'public');
            $dims = $file && $data['media_type'] === 'image' ? @getimagesize($file->getRealPath()) : null;

            $media = MediaAsset::create([
                'subject_type' => $data['subject_type'], 'subject_id' => $subject->id, 'media_type' => $data['media_type'],
                'locale' => $data['locale'] ?? null, 'path' => $path ?: null, 'external_url' => $data['external_url'] ?? null,
                'mime' => $file?->getMimeType(), 'size_bytes' => $file?->getSize(),
                'width' => $dims[0] ?? null, 'height' => $dims[1] ?? null,
                'sort_order' => (int) MediaAsset::where('subject_type', $data['subject_type'])->where('subject_id', $subject->id)->max('sort_order') + 1,
                'is_cover' => (bool) ($data['is_cover'] ?? false),
                'source_name' => $data['source_name'], 'source_url' => $data['source_url'] ?? null, 'author' => $data['author'] ?? null,
                'license' => $data['license'], 'rights_holder' => $data['rights_holder'] ?? null,
                'captured_at' => $data['captured_at'] ?? null, 'provenance_note' => $data['provenance_note'] ?? null,
                'uploaded_by' => $request->user()->id,
            ]);
            foreach (['vi', 'en'] as $locale) {
                $caption = $data['caption_'.$locale] ?? null;
                if ($caption) {
                    $media->captions()->create(['locale' => $locale, 'caption' => $caption, 'transcript' => $data['transcript_'.$locale] ?? null]);
                }
            }
            if ($media->is_cover) {
                $this->setCover($media, $subject);
            }

            return $media;
        });
        $audit->log('admin.media.create', $media, ['subject' => $data['subject_type'].'#'.$subject->id]);

        return back()->with('success', __('messages.admin.media_uploaded'));
    }

    public function destroy(MediaAsset $media, AuditLogger $audit): RedirectResponse
    {
        if ($media->path) {
            Storage::disk('public')->delete($media->path);
        }
        $audit->log('admin.media.delete', $media, ['path' => $media->path]);
        $media->delete();

        return back()->with('success', __('messages.admin.deleted'));
    }

    private function setCover(MediaAsset $media, $subject): void
    {
        MediaAsset::query()->where('subject_type', $media->subject_type)->where('subject_id', $media->subject_id)
            ->whereKeyNot($media->id)->update(['is_cover' => false]);
        $column = $media->subject_type === 'figure' ? 'portrait_media_id' : 'cover_media_id';
        $subject->forceFill([$column => $media->id])->save();
    }

    private function subject(MediaAsset $m): ?array
    {
        $subject = $m->subject;
        if (! $subject || ! isset(self::SUBJECT_ROUTES[$m->subject_type])) {
            return null;
        }
        [$name, $param] = self::SUBJECT_ROUTES[$m->subject_type];

        return [
            'type' => $m->subject_type,
            'id' => $subject->id,
            'title' => $m->subject_type === 'route' ? $subject->name : $subject->title,
            'url' => localized_route($name, [$param => $subject->slug]),
        ];
    }
}
