<?php

namespace App\Http\Controllers;

use App\Http\Resources\ArtifactCardResource;
use App\Http\Resources\FigureCardResource;
use App\Http\Resources\RouteCardResource;
use App\Http\Resources\SiteCardResource;
use App\Models\Artifact;
use App\Models\HeritageSite;
use App\Models\HistoricalFigure;
use App\Models\TourRoute;
use App\Services\LookupService;
use Inertia\Inertia;
use Inertia\Response;

class HomeController extends Controller
{
    public function __invoke(LookupService $lookup): Response
    {
        $featured = HeritageSite::published()->with(SiteCardResource::eager())
            ->orderByRaw("FIELD(recognition_level, 'unesco', 'quoc_gia_dac_biet', 'quoc_gia', 'cap_tinh', 'chua_xep_hang')")
            ->orderByDesc('view_count')->limit(6)->get();

        $artifacts = Artifact::published()->with(ArtifactCardResource::eager())->latest('id')->limit(6)->get();
        $figures = HistoricalFigure::published()->with(FigureCardResource::eager())->orderByDesc('view_count')->limit(4)->get();
        $routes = TourRoute::published()->with(RouteCardResource::eager())->withCount('stops')
            ->whereHas('slots', fn ($q) => $q->upcoming())->limit(3)->get();

        return Inertia::render('Home', [
            'featuredSites' => SiteCardResource::list($featured),
            'latestArtifacts' => ArtifactCardResource::list($artifacts),
            'upcomingRoutes' => RouteCardResource::list($routes),
            'featuredFigures' => FigureCardResource::list($figures),
            'stats' => $lookup->stats(),
            'mapPoints' => MapPoints::all(),
        ]);
    }
}
