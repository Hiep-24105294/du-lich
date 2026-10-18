<?php

namespace App\Services\Queries;

use App\Models\Artifact;
use App\Models\HeritageSite;
use App\Models\HistoricalFigure;
use Illuminate\Database\Eloquent\Builder;

/**
 * Truy vấn lọc dùng chung cho trang web và API mở. Mọi giá trị lọc đi qua binding,
 * thứ tự sắp xếp chỉ chọn trong danh sách cố định.
 */
class HeritageQuery
{
    public static function sites(array $f): Builder
    {
        $query = HeritageSite::query()->published();

        self::keyword($query, $f['q'] ?? null);
        $query->when($f['province'] ?? null, fn ($q, $v) => $q->whereHas('province', fn ($p) => $p->where('code', $v)))
            ->when($f['type'] ?? null, fn ($q, $v) => $q->where('site_type', $v))
            ->when($f['level'] ?? null, fn ($q, $v) => $q->where('recognition_level', $v))
            ->when($f['period'] ?? null, fn ($q, $v) => $q->whereHas('period', fn ($p) => $p->where('code', $v)))
            ->when($f['tag'] ?? null, fn ($q, $v) => $q->withTag($v));

        return match ($f['sort'] ?? null) {
            'popular' => $query->orderByDesc('view_count')->orderBy('id'),
            'newest' => $query->orderByDesc('created_at')->orderByDesc('id'),
            'year' => $query->orderByRaw('year_built IS NULL')->orderBy('year_built')->orderBy('id'),
            default => $query->orderByRaw("FIELD(recognition_level, 'unesco', 'quoc_gia_dac_biet', 'quoc_gia', 'cap_tinh', 'chua_xep_hang')")
                ->orderByDesc('view_count')->orderBy('id'),
        };
    }

    public static function artifacts(array $f): Builder
    {
        $query = Artifact::query()->published()->whereHas('site', fn ($s) => $s->published());

        self::keyword($query, $f['q'] ?? null);
        $query->when($f['type'] ?? null, fn ($q, $v) => $q->where('artifact_type', $v))
            ->when($f['site'] ?? null, fn ($q, $v) => $q->whereHas('site', fn ($s) => $s->where('slug', $v)))
            ->when(($f['treasure'] ?? null) !== null, fn ($q) => $q->where('is_national_treasure', filter_var($f['treasure'], FILTER_VALIDATE_BOOLEAN)))
            ->when($f['period'] ?? null, fn ($q, $v) => $q->whereHas('period', fn ($p) => $p->where('code', $v)))
            ->when($f['tag'] ?? null, fn ($q, $v) => $q->withTag($v));

        return $query->orderByDesc('is_national_treasure')->orderByDesc('view_count')->orderBy('id');
    }

    public static function figures(array $f): Builder
    {
        $query = HistoricalFigure::query()->published();

        self::keyword($query, $f['q'] ?? null);
        $query->when($f['type'] ?? null, fn ($q, $v) => $q->where('figure_type', $v))
            ->when($f['period'] ?? null, fn ($q, $v) => $q->whereHas('period', fn ($p) => $p->where('code', $v)))
            ->when($f['tag'] ?? null, fn ($q, $v) => $q->withTag($v));

        return $query->orderByRaw('birth_year IS NULL')->orderBy('birth_year')->orderBy('id');
    }

    /** Tìm theo tiêu đề/tóm tắt diễn giải (mọi ngôn ngữ) hoặc slug, LIKE tham số hóa. */
    public static function keyword(Builder $query, ?string $q): void
    {
        if ($q === null || trim($q) === '') {
            return;
        }
        $like = '%'.str_replace(['\\', '%', '_'], ['\\\\', '\\%', '\\_'], trim($q)).'%';
        $query->where(fn ($w) => $w->where('slug', 'like', $like)
            ->orWhereHas('interpretations', fn ($i) => $i->where(fn ($x) => $x->where('title', 'like', $like)->orWhere('summary', 'like', $like))));
    }
}
