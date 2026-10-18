<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Vite;
use Symfony\Component\HttpFoundation\Response;

/**
 * Header bảo mật: chống clickjacking, MIME sniffing, giới hạn nguồn tài nguyên (CSP).
 */
class SecurityHeaders
{
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        $headers = $response->headers;
        $headers->set('X-Frame-Options', 'DENY');
        $headers->set('X-Content-Type-Options', 'nosniff');
        $headers->set('Referrer-Policy', 'strict-origin-when-cross-origin');
        $headers->set('Permissions-Policy', 'camera=(self), microphone=(), geolocation=(self), payment=()');
        $headers->set('Content-Security-Policy', $this->csp());

        if ($request->secure()) {
            $headers->set('Strict-Transport-Security', 'max-age=31536000; includeSubDomains');
        }

        return $response;
    }

    private function csp(): string
    {
        $nonce = Vite::cspNonce();
        $dev = app()->isLocal() ? ' http://localhost:5173 ws://localhost:5173 http://127.0.0.1:5173 ws://127.0.0.1:5173' : '';

        return implode('; ', [
            "default-src 'self'",
            "script-src 'self' 'nonce-{$nonce}' https://unpkg.com https://ajax.googleapis.com{$dev}",
            "style-src 'self' 'unsafe-inline' https://fonts.googleapis.com https://unpkg.com{$dev}",
            "font-src 'self' data: https://fonts.gstatic.com",
            "img-src 'self' data: blob: https://*.tile.openstreetmap.org https://tile.openstreetmap.org https://unpkg.com https:",
            "media-src 'self' data: blob: https:",
            "connect-src 'self' https://unpkg.com https://modelviewer.dev https://*.githubusercontent.com https://www.gstatic.com{$dev}",
            "worker-src 'self' blob:",
            "object-src 'none'",
            "base-uri 'self'",
            "form-action 'self'",
            "frame-ancestors 'none'",
        ]);
    }
}
