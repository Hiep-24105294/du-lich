<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" data-theme="light">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="color-scheme" content="light dark">
    <link rel="icon" href="/favicon.ico">
    {{-- Đặt theme trước khi render để tránh nháy màu --}}
    <script nonce="{{ Vite::cspNonce() }}">
        (function () {
            try {
                var t = localStorage.getItem('disanso-theme');
                if (t !== 'light' && t !== 'dark') {
                    t = (window.matchMedia && window.matchMedia('(prefers-color-scheme: dark)').matches) ? 'dark' : 'light';
                }
                document.documentElement.setAttribute('data-theme', t);
            } catch (e) {}
        })();
    </script>
    {{-- Font tự host qua app.css (font-display: swap); preload chunk của trang hiện tại để không phải chờ app.js mới biết cần tải gì --}}
    @php
        $pageComponent = $page['component'] ?? null;
        $pageFile = $pageComponent ? "resources/js/Pages/{$pageComponent}.vue" : null;
        $layoutFile = match (true) {
            $pageComponent === null => null,
            str_starts_with($pageComponent, 'Admin/') => 'resources/js/Layouts/AdminLayout.vue',
            str_starts_with($pageComponent, 'Auth/') => 'resources/js/Layouts/AuthLayout.vue',
            default => 'resources/js/Layouts/PublicLayout.vue',
        };
        $pageAssets = array_values(array_filter(
            ['resources/css/app.css', 'resources/js/app.js', $layoutFile, $pageFile],
            fn ($f) => $f !== null && is_file(resource_path(substr($f, strlen('resources/')))),
        ));
    @endphp
    @vite($pageAssets)
    @inertiaHead
</head>
<body class="antialiased">
    @inertia
</body>
</html>
