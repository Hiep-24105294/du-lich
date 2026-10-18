<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ContributionItemResource extends BaseResource
{
    public const SUBJECT_ROUTES = [
        'site' => ['sites.show', 'site'],
        'artifact' => ['artifacts.show', 'artifact'],
        'figure' => ['figures.show', 'figure'],
    ];

    public static function eager(): array
    {
        return ['user', 'subject' => fn ($q) => $q->with('interpretations')];
    }

    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'contribution_type' => $this->contribution_type,
            'locale' => $this->locale,
            'title' => $this->title,
            'body' => $this->body,
            'attachment_url' => $this->attachment_path ? Storage::disk('public')->url($this->attachment_path) : null,
            'suggested_tags' => collect($this->suggested_tags ?? [])->map(fn ($t) => [
                'slug' => $t['slug'] ?? null,
                'name' => $t['name'] ?? ($t['slug'] ?? null),
                'score' => isset($t['score']) ? round((float) $t['score'], 3) : null,
            ])->values()->all(),
            'status' => $this->status,
            'review_note' => $this->review_note,
            'reviewed_at' => $this->reviewed_at?->toIso8601String(),
            'created_at' => $this->created_at?->toIso8601String(),
            'user' => ['name' => $this->user?->name],
            'subject' => $this->subjectData(),
            'url' => localized_route('contributions.show', ['contribution' => $this->id]),
        ];
    }

    private function subjectData(): ?array
    {
        $subject = $this->subject_type ? $this->subject : null;
        if (! $subject || ! isset(self::SUBJECT_ROUTES[$this->subject_type])) {
            return null;
        }
        [$name, $param] = self::SUBJECT_ROUTES[$this->subject_type];

        return [
            'type' => $this->subject_type,
            'id' => $subject->id,
            'slug' => $subject->slug,
            'title' => $subject->title,
            'url' => localized_route($name, [$param => $subject->slug]),
        ];
    }
}
