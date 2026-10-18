<?php

namespace App\Http\Controllers;

use App\Http\Resources\SiteCardResource;
use App\Http\Resources\SiteDetailResource;
use App\Models\HeritageSite;
use App\Services\ContentLocalizer;
use App\Services\LookupService;
use App\Services\PageViewRecorder;
use App\Services\Queries\HeritageQuery;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class SiteController extends Controller
{
    public const FILTERS = ['q', 'province', 'type', 'level', 'period', 'tag', 'sort'];

    public function index(Request $request, LookupService $lookup): Response
    {
        $filters = $this->filters($request, self::FILTERS);
        $sites = HeritageQuery::sites($filters)->with(SiteCardResource::eager())->paginate(12)->withQueryString();

        return Inertia::render('Sites/Index', [
            'sites' => SiteCardResource::paginate($sites),
            'filters' => $filters,
            'provinces' => $lookup->provinces(true),
            'periods' => $lookup->periods(),
            'tags' => $lookup->tags('chu_de'),
            'types' => $this->enumOptions('site_types', HeritageSite::TYPES),
            'levels' => $this->enumOptions('levels', HeritageSite::LEVELS),
            'mapPoints' => MapPoints::all(HeritageQuery::sites($filters)),
        ]);
    }

    public function show(Request $request, HeritageSite $site, ContentLocalizer $content, PageViewRecorder $views): Response
    {
        abort_unless($site->status === 'published', 404);
        $depth = $content->normalizeDepth($request->query('depth', 1));

        $site->load(SiteDetailResource::eager());
        $views->record($site, $request);

        $related = HeritageSite::published()->whereKeyNot($site->id)
            ->where(fn ($q) => $q->where('province_id', $site->province_id)
                ->orWhereHas('tags', fn ($t) => $t->whereIn('tags.id', $site->tags->pluck('id'))))
            ->with(SiteCardResource::eager())->orderByDesc('view_count')->limit(4)->get();

        return Inertia::render('Sites/Show', [
            'site' => SiteDetailResource::detail($site, $depth),
            'depth' => $depth,
            'related' => SiteCardResource::list($related),
            'isFavorite' => FavoriteController::isFavorite($request->user(), $site),
        ]);
    }
}
