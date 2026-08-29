<?php

declare(strict_types=1);

namespace App\Actions\Link;

use App\Models\Link;

class BuildDestination
{
    /**
     * The UTM parameters a link can carry onto its destination.
     *
     * @var array<int, string>
     */
    public const PARAMETERS = [
        'utm_source',
        'utm_medium',
        'utm_campaign',
        'utm_term',
        'utm_content',
    ];

    /**
     * Put the link's UTM parameters on the URL the visitor is being sent to.
     *
     * Without this the columns are write-only: the redirect used to hand over
     * `$link->url` untouched, so a campaign configured on the link never
     * reached the destination's own analytics.
     *
     * Three rules, in order of precedence:
     *  - a parameter already on the destination URL is kept, because whoever
     *    wrote that URL was explicit about it;
     *  - the link's UTMs fill in the rest;
     *  - a UTM on the incoming request wins over both, since it describes the
     *    visit that actually happened rather than the campaign's default.
     *
     * @param  array<string, string|null>  $incoming  UTMs from the request's query string.
     */
    public static function execute(string $url, Link $link, array $incoming = []): string
    {
        $parts = parse_url($url);

        if ($parts === false || ! isset($parts['host'])) {
            return $url;
        }

        parse_str($parts['query'] ?? '', $query);

        foreach (self::PARAMETERS as $parameter) {
            $fromRequest = data_get($incoming, $parameter);

            if (filled($fromRequest)) {
                $query[$parameter] = $fromRequest;

                continue;
            }

            if (filled($link->{$parameter}) && blank(data_get($query, $parameter))) {
                $query[$parameter] = $link->{$parameter};
            }
        }

        if ($query === []) {
            return $url;
        }

        return self::rebuild($parts, $query);
    }

    /**
     * @param  array<string, mixed>  $parts
     * @param  array<string, mixed>  $query
     */
    private static function rebuild(array $parts, array $query): string
    {
        $rebuilt = ($parts['scheme'] ?? 'https').'://';

        if (isset($parts['user'])) {
            $rebuilt .= $parts['user'].(isset($parts['pass']) ? ':'.$parts['pass'] : '').'@';
        }

        $rebuilt .= $parts['host'];

        if (isset($parts['port'])) {
            $rebuilt .= ':'.$parts['port'];
        }

        $rebuilt .= $parts['path'] ?? '';
        $rebuilt .= '?'.http_build_query($query);

        if (isset($parts['fragment'])) {
            $rebuilt .= '#'.$parts['fragment'];
        }

        return $rebuilt;
    }
}
