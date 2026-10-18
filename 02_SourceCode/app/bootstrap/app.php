<?php

use App\Http\Controllers\HealthController;
use App\Http\Middleware\EnsureRole;
use App\Http\Middleware\HandleInertiaRequests;
use App\Http\Middleware\SecurityHeaders;
use App\Http\Middleware\SetLocale;
use App\Services\Payment\PaymentException;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Exceptions\ThrottleRequestsException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        apiPrefix: 'api',
        then: function () {
            // Health check tự viết: không qua session/CSRF để Docker gọi nhanh
            Route::get('/healthz', HealthController::class)->name('healthz');
        },
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->trustProxies(at: '*');

        $middleware->alias([
            'role' => EnsureRole::class,
            'locale' => SetLocale::class,
        ]);

        $middleware->web(append: [
            SetLocale::class,
            HandleInertiaRequests::class,
            SecurityHeaders::class,
        ]);

        $middleware->api(prepend: ['throttle:api']);

        $middleware->redirectGuestsTo(fn (Request $request) => localized_route('login'));
        $middleware->redirectUsersTo(fn (Request $request) => localized_route('account.dashboard'));
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        // Chữ ký callback thanh toán sai → 400, không tiết lộ chi tiết
        $exceptions->render(function (PaymentException $e, Request $request) {
            if ($e->getCode() === 400) {
                return $request->expectsJson()
                    ? response()->json(['message' => $e->getMessage()], 400)
                    : response($e->getMessage(), 400);
            }

            return null;
        });

        // Quá số lần đăng nhập: báo lỗi ngay trên ô email thay vì trang 429 trống
        $exceptions->render(function (ThrottleRequestsException $e, Request $request) {
            if ($request->routeIs('login.store') && ! $request->expectsJson()) {
                $seconds = (int) ($e->getHeaders()['Retry-After'] ?? 60);

                return back()->withInput($request->only('email'))
                    ->withErrors(['email' => __('auth.throttle', ['seconds' => $seconds])]);
            }

            return null;
        });
    })->create();
