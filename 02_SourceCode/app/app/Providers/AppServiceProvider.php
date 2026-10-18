<?php

namespace App\Providers;

use App\Models\Artifact;
use App\Models\Booking;
use App\Models\Contribution;
use App\Models\HeritageSite;
use App\Models\HistoricalFigure;
use App\Models\MediaAsset;
use App\Models\Payment;
use App\Models\Tag;
use App\Models\TourRoute;
use App\Models\TourSlot;
use App\Models\User;
use App\Policies\BookingPolicy;
use App\Policies\ContributionPolicy;
use App\Policies\TourRoutePolicy;
use App\Services\Payment\PaymentGateway;
use App\Services\Payment\SandboxGateway;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Vite;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Str;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(PaymentGateway::class, SandboxGateway::class);
        // Bảng periods nhỏ, nạp một lần cho mỗi request/lệnh seed
        $this->app->scoped(\App\Services\PeriodResolver::class);
    }

    public function boot(): void
    {
        // Cột *_type lưu alias ngắn thay vì tên lớp, tránh lộ cấu trúc mã và gọn dữ liệu
        Relation::enforceMorphMap([
            'site' => HeritageSite::class,
            'artifact' => Artifact::class,
            'figure' => HistoricalFigure::class,
            'route' => TourRoute::class,
            'user' => User::class,
            // Alias bổ sung cho nhật ký kiểm toán và tệp đa phương tiện
            'booking' => Booking::class,
            'payment' => Payment::class,
            'contribution' => Contribution::class,
            'tag' => Tag::class,
            'media' => MediaAsset::class,
            'slot' => TourSlot::class,
        ]);

        // Resource tự bọc "data" chỉ ở API; Inertia nhận mảng phẳng
        JsonResource::withoutWrapping();

        $this->registerAuthorization();
        $this->registerRateLimiters();

        Vite::useCspNonce();
        Vite::prefetch(concurrency: 3);
    }

    private function registerAuthorization(): void
    {
        Gate::policy(TourRoute::class, TourRoutePolicy::class);
        Gate::policy(Booking::class, BookingPolicy::class);
        Gate::policy(Contribution::class, ContributionPolicy::class);

        Gate::define('manage-content', fn (User $user) => $user->isStaff());
        Gate::define('manage-users', fn (User $user) => $user->isAdmin());
        Gate::define('view-audit', fn (User $user) => $user->isAdmin());
    }

    private function registerRateLimiters(): void
    {
        // Chống dò mật khẩu: 5 lần/phút cho mỗi cặp email + IP
        RateLimiter::for('login', function (Request $request) {
            $key = Str::lower((string) $request->input('email')).'|'.$request->ip();

            return Limit::perMinute(5)->by($key);
        });

        RateLimiter::for('api', fn (Request $request) => Limit::perMinute(60)->by($request->ip()));
    }
}
