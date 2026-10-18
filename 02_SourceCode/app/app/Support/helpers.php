<?php

if (! function_exists('localized_route')) {
    /**
     * Sinh URL tuyệt đối có tiền tố locale (mặc định locale hiện tại).
     * Tham số `locale` trong $params ghi đè locale hiện tại.
     */
    function localized_route(string $name, mixed $params = [], bool $absolute = true): string
    {
        $params = is_array($params) ? $params : [$params];
        $locale = $params['locale'] ?? app()->getLocale();
        unset($params['locale']);
        if (! in_array($locale, ['vi', 'en'], true)) {
            $locale = config('app.locale', 'vi');
        }

        return route($name, array_merge(['locale' => $locale], $params), $absolute);
    }
}
