<?php

declare(strict_types=1);

namespace App\Actions\Tool;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use Throwable;

/**
 * Follow a URL's redirect chain and report each hop.
 *
 * This fetches a URL chosen by whoever is on the page, from our server, which
 * is the textbook shape of a server-side request forgery. Every guard below
 * exists for that reason rather than for tidiness:
 *
 *  - only http and https, so `file://` and friends cannot be reached;
 *  - the host is resolved and every address it answers with is checked against
 *    private, loopback, link-local and reserved ranges **on every hop**,
 *    because a public URL is free to redirect to 127.0.0.1 or to a cloud
 *    metadata address;
 *  - redirects are followed one at a time rather than by the HTTP client,
 *    which is what makes that per-hop check possible at all;
 *  - a hop limit and a short timeout, so a redirect loop cannot hold a worker;
 *  - the response body is never read and never returned. Only the status and
 *    the location.
 */
class FollowRedirects
{
    private const MAX_HOPS = 10;

    private const TIMEOUT_SECONDS = 5;

    /**
     * @return array{hops: list<array{url: string, status: int|null, error: string|null}>, destination: string|null, error: string|null}
     */
    public static function execute(string $url): array
    {
        $hops = [];
        $current = $url;

        for ($hop = 0; $hop < self::MAX_HOPS; $hop++) {
            $rejection = self::reject($current);

            if ($rejection !== null) {
                $hops[] = ['url' => $current, 'status' => null, 'error' => $rejection];

                return ['hops' => $hops, 'destination' => null, 'error' => $rejection];
            }

            try {
                $response = Http::withoutRedirecting()
                    ->timeout(self::TIMEOUT_SECONDS)
                    ->withHeaders(['User-Agent' => 'Lua link checker (+https://lua.sh)'])
                    ->get($current);
            } catch (Throwable) {
                $hops[] = ['url' => $current, 'status' => null, 'error' => 'Could not be reached.'];

                return ['hops' => $hops, 'destination' => null, 'error' => 'Could not be reached.'];
            }

            $status = $response->status();
            $hops[] = ['url' => $current, 'status' => $status, 'error' => null];

            $location = (string) $response->header('Location');

            if ($status < 300 || $status >= 400 || $location === '') {
                return ['hops' => $hops, 'destination' => $current, 'error' => null];
            }

            $current = self::resolve($current, $location);
        }

        return [
            'hops' => $hops,
            'destination' => null,
            'error' => 'Stopped after '.self::MAX_HOPS.' redirects. This link loops.',
        ];
    }

    /**
     * A `Location` header may be absolute, root-relative or path-relative, and
     * all three are legal. Resolving it against the hop it came from is what
     * keeps the per-hop address check meaningful: a relative redirect left
     * unresolved would be checked as a hostless string and pass.
     */
    private static function resolve(string $from, string $location): string
    {
        if (Str::startsWith($location, ['http://', 'https://'])) {
            return $location;
        }

        $parts = parse_url($from);
        $origin = ($parts['scheme'] ?? 'https').'://'.($parts['host'] ?? '')
            .(isset($parts['port']) ? ':'.$parts['port'] : '');

        if (str_starts_with($location, '/')) {
            return $origin.$location;
        }

        $directory = Str::beforeLast($parts['path'] ?? '/', '/');

        return $origin.$directory.'/'.$location;
    }

    /**
     * The reason this URL may not be fetched, or null when it may.
     */
    private static function reject(string $url): ?string
    {
        $parts = parse_url($url);
        $scheme = strtolower($parts['scheme'] ?? '');
        $host = $parts['host'] ?? '';

        if (! in_array($scheme, ['http', 'https'], true) || $host === '') {
            return 'Only http and https addresses can be checked.';
        }

        // Every address the host answers with has to be public: a name can
        // resolve to several, and one private answer is enough to reach an
        // internal service.
        $addresses = filter_var($host, FILTER_VALIDATE_IP)
            ? [$host]
            : (gethostbynamel($host) ?: []);

        if ($addresses === []) {
            return 'That host does not resolve.';
        }

        foreach ($addresses as $address) {
            $public = filter_var(
                $address,
                FILTER_VALIDATE_IP,
                FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE,
            );

            if ($public === false) {
                return 'That address is on a private network and will not be fetched.';
            }
        }

        return null;
    }
}
