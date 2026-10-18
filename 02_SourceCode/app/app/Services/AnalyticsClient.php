<?php

namespace App\Services;

use Illuminate\Http\Client\PendingRequest;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Throwable;

/**
 * Gọi dịch vụ Python nội bộ. Mọi lỗi/timeout trả null để tầng trên dùng phương án dự phòng.
 * Trạng thái up/down được nhớ 60 giây để không chờ timeout ở mỗi yêu cầu.
 */
class AnalyticsClient
{
    public const STATUS_KEY = 'analytics:up';

    public function isUp(): bool
    {
        return (bool) Cache::remember(self::STATUS_KEY, $this->ttl(), function () {
            return $this->send('get', '/healthz') !== null;
        });
    }

    public function search(string $q, string $locale, int $limit = 20, ?array $types = null): ?array
    {
        $body = array_filter(['q' => $q, 'locale' => $locale, 'limit' => $limit, 'types' => $types], fn ($v) => $v !== null);
        $data = $this->call('post', '/search', $body);

        return is_array($data['results'] ?? null) ? $data : null;
    }

    public function suggestTags(string $text, string $locale, int $limit = 5): ?array
    {
        $data = $this->call('post', '/suggest-tags', ['text' => $text, 'locale' => $locale, 'limit' => $limit]);

        return is_array($data['suggestions'] ?? null) ? $data['suggestions'] : null;
    }

    public function qualityReport(): ?array
    {
        $data = $this->call('get', '/quality-report');

        return is_array($data['checks'] ?? null) ? $data : null;
    }

    public function stats(): ?array
    {
        return $this->call('get', '/stats/overview');
    }

    public function reindex(): ?array
    {
        return $this->call('post', '/reindex');
    }

    /** Bỏ qua cuộc gọi nếu vừa biết dịch vụ đang tắt. */
    private function call(string $method, string $path, array $body = []): ?array
    {
        if (Cache::get(self::STATUS_KEY) === false) {
            return null;
        }
        $data = $this->send($method, $path, $body);
        Cache::put(self::STATUS_KEY, $data !== null, $this->ttl());

        return $data;
    }

    private function send(string $method, string $path, array $body = []): ?array
    {
        try {
            $response = $method === 'get'
                ? $this->client()->get($path, $body)
                : $this->client()->post($path, $body);

            if (! $response->successful()) {
                Log::warning('Analytics service error', ['path' => $path, 'status' => $response->status()]);

                return null;
            }
            $json = $response->json();

            return is_array($json) ? $json : null;
        } catch (Throwable $e) {
            Log::warning('Analytics service unavailable', ['path' => $path, 'error' => $e->getMessage()]);

            return null;
        }
    }

    private function client(): PendingRequest
    {
        $timeout = (float) config('disanso.analytics.timeout', 2);

        return Http::baseUrl(rtrim((string) config('disanso.analytics.url'), '/'))
            ->timeout($timeout)
            ->connectTimeout($timeout)
            ->acceptJson()
            ->withHeaders(['X-Analytics-Token' => (string) config('disanso.analytics.token')]);
    }

    private function ttl(): int
    {
        return (int) config('disanso.analytics.status_ttl', 60);
    }
}
