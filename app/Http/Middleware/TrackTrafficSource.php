<?php

namespace App\Http\Middleware;

use App\Models\UtmVisit;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\Response;

class TrackTrafficSource
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        if (! $this->shouldTrack($request)) {
            return $response;
        }

        $session = $request->session();
        if ($session->get('traffic_source_tracked')) {
            return $response;
        }

        $utmData = $this->extractUtmData($request);
        $referrer = $request->headers->get('referer');
        $source = $this->resolveSource($request, $utmData['utm_source'] ?? null, $referrer);

        UtmVisit::create([
            'source' => $source,
            'utm_source' => $utmData['utm_source'] ?? null,
            'utm_medium' => $utmData['utm_medium'] ?? null,
            'utm_campaign' => $utmData['utm_campaign'] ?? null,
            'utm_term' => $utmData['utm_term'] ?? null,
            'utm_content' => $utmData['utm_content'] ?? null,
            'referrer_host' => $this->extractReferrerHost($referrer),
            'landing_page' => $request->fullUrl(),
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'user_id' => Auth::id(),
            'session_id' => $session->getId(),
        ]);

        $session->put('traffic_source_tracked', true);

        return $response;
    }

    private function shouldTrack(Request $request): bool
    {
        if (! $request->isMethod('get')) {
            return false;
        }

        if (! $request->hasSession()) {
            return false;
        }

        if ($request->ajax() || $request->expectsJson()) {
            return false;
        }

        return true;
    }

    /**
     * @return array<string, string|null>
     */
    private function extractUtmData(Request $request): array
    {
        $keys = [
            'utm_source',
            'utm_medium',
            'utm_campaign',
            'utm_term',
            'utm_content',
        ];

        $data = [];
        foreach ($keys as $key) {
            $value = $request->query($key);
            $data[$key] = $value !== null ? (string) $value : null;
        }

        return $data;
    }

    private function resolveSource(Request $request, ?string $utmSource, ?string $referrer): string
    {
        $normalized = $this->normalizeSource($utmSource);
        if ($normalized !== null) {
            return $normalized;
        }

        return $this->sourceFromReferrer($request, $referrer);
    }

    private function normalizeSource(?string $source): ?string
    {
        if ($source === null || trim($source) === '') {
            return null;
        }

        $source = strtolower(trim($source));

        $mapping = [
            'facebook' => 'facebook',
            'fb' => 'facebook',
            'instagram' => 'instagram',
            'ig' => 'instagram',
            'google' => 'google',
            'gads' => 'google',
            'googleads' => 'google',
            'adwords' => 'google',
            'bing' => 'bing',
            'yahoo' => 'yahoo',
            'tiktok' => 'tiktok',
            'youtube' => 'youtube',
            'linkedin' => 'linkedin',
            'twitter' => 'twitter',
            'x' => 'twitter',
        ];

        foreach ($mapping as $needle => $value) {
            if (Str::contains($source, $needle)) {
                return $value;
            }
        }

        return $source;
    }

    private function sourceFromReferrer(Request $request, ?string $referrer): string
    {
        $host = $this->extractReferrerHost($referrer);
        if ($host === null) {
            return 'direct';
        }

        $currentHost = strtolower($request->getHost());
        if ($host === $currentHost) {
            return 'direct';
        }

        $mapping = [
            'facebook.com' => 'facebook',
            'fb.com' => 'facebook',
            'instagram.com' => 'instagram',
            'tiktok.com' => 'tiktok',
            'youtube.com' => 'youtube',
            'youtu.be' => 'youtube',
            'linkedin.com' => 'linkedin',
            'twitter.com' => 'twitter',
            't.co' => 'twitter',
            'bing.com' => 'bing',
            'yahoo.com' => 'yahoo',
        ];

        foreach ($mapping as $needle => $value) {
            if (Str::contains($host, $needle)) {
                return $value;
            }
        }

        if (Str::contains($host, 'google.')) {
            return 'google';
        }

        return 'referral';
    }

    private function extractReferrerHost(?string $referrer): ?string
    {
        if ($referrer === null || trim($referrer) === '') {
            return null;
        }

        $host = parse_url($referrer, PHP_URL_HOST);
        if (! is_string($host) || $host === '') {
            return null;
        }

        $host = strtolower($host);
        $host = preg_replace('/^www\./', '', $host);

        return $host ?: null;
    }
}
