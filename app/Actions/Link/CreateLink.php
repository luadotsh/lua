<?php

declare(strict_types=1);

namespace App\Actions\Link;

use App\Models\Link;
use App\Models\User;
use App\Models\Workspace;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class CreateLink
{
    /**
     * The plan's link allowance for the billing cycle.
     *
     * Enforced here rather than in the controllers because the web form, the
     * REST API and the MCP tool all come through this action — a check in one
     * of them would be a limit the other two ignore.
     *
     * Reported as a validation error on `url` so it lands on the one field the
     * create dialog has, and comes back as a 422 with a readable message to
     * the API and to MCP.
     */
    private static function assertWithinPlanLimit(Workspace $workspace): void
    {
        $links = $workspace->usage()['links'];

        if (! $links['reached_limit']) {
            return;
        }

        throw ValidationException::withMessages([
            'url' => "Your plan covers {$links['limit']} links per billing cycle and you have used them all. Upgrade to create more.",
        ]);
    }

    /**
     * The creator is passed in rather than read from `auth()`: an MCP tool call
     * and an API request are not the web session, and the action has to work
     * the same for all three.
     *
     * @param  array<string, mixed>  $data
     */
    public static function execute(Workspace $workspace, array $data, ?User $creator = null): Link
    {
        self::assertWithinPlanLimit($workspace);

        $domain = data_get($data, 'domain') ?: config('domains.main');
        $key = data_get($data, 'key') ?: Str::random(7);

        return DB::transaction(function () use ($workspace, $data, $domain, $key, $creator): Link {
            $link = Link::create([
                'workspace_id' => $workspace->id,
                'user_id' => $creator?->id,
                'domain' => $domain,
                'key' => $key,
                'url' => data_get($data, 'url'),
                'link' => "https://{$domain}/{$key}",
                'ios' => data_get($data, 'ios'),
                'android' => data_get($data, 'android'),
                'utm_source' => data_get($data, 'utm_source'),
                'utm_medium' => data_get($data, 'utm_medium'),
                'utm_campaign' => data_get($data, 'utm_campaign'),
                'utm_term' => data_get($data, 'utm_term'),
                'utm_content' => data_get($data, 'utm_content'),
                'password' => data_get($data, 'password'),
                'external_id' => data_get($data, 'external_id'),
                'expires_at' => data_get($data, 'expires_at'),
                'expired_redirect_url' => data_get($data, 'expired_redirect_url'),
            ]);

            $link->tags()->sync(data_get($data, 'tags') ?? []);

            return $link->load('tags');
        });
    }

    /**
     * @return array<string, string>
     */
    public static function messages(): array
    {
        return [
            'key.alpha_dash' => 'The custom back-half may only contain letters, numbers, hyphens and underscores.',
        ];
    }

    /**
     * Shared by the REST form requests and the MCP tools, so both surfaces
     * enforce the same thing. Context is passed in rather than read off a
     * request, because a tool call is not an HTTP request.
     *
     * @param  array<string, mixed>  $input
     * @return array<string, mixed>
     */
    public static function rules(?Workspace $workspace, array $input, string|int|null $ignoreId = null): array
    {
        $domains = array_merge(
            $workspace?->domains->pluck('domain')->toArray() ?? [],
            config('domains.available'),
        );

        // A link keeps the domain it was created on even after that domain
        // leaves the workspace. Without this, removing a custom domain makes
        // every link on it uneditable — you could not even fix its destination,
        // because the unchanged domain would fail the check.
        if ($ignoreId !== null && $workspace !== null) {
            $current = Link::where('workspace_id', $workspace->id)->find($ignoreId);

            if ($current) {
                $domains[] = $current->domain;
            }
        }

        $domain = data_get($input, 'domain');
        $key = data_get($input, 'key');

        $optional = fn (mixed $value, array $rules) => Rule::when(fn () => filled($value), $rules);

        return [
            'key' => $optional($key, [
                'required', 'string', 'max:255',
                // Letters, numbers, hyphens and underscores. `:ascii` is the
                // point: without it alpha_dash also accepts unicode letters,
                // which cannot sit in a path without percent-encoding.
                // Case is significant — lookups match the column exactly, so
                // `/AbC` and `/abc` are two different links.
                'alpha_dash:ascii',
                Rule::unique('links')->where('domain', $domain)->ignore($ignoreId),
            ]),
            'domain' => [
                'required', 'string', 'max:255', 'min:2',
                Rule::in($domains),
                Rule::unique('links')->where('key', $key)->ignore($ignoreId),
            ],
            'url' => ['required', 'url', 'max:255', 'min:2'],
            'ios' => ['nullable', 'url', 'max:255', 'min:2'],
            'android' => ['nullable', 'url', 'max:255', 'min:2'],
            'utm_source' => $optional(data_get($input, 'utm_source'), ['required', 'string', 'max:255', 'min:2']),
            'utm_medium' => $optional(data_get($input, 'utm_medium'), ['required', 'string', 'max:255', 'min:2']),
            'utm_campaign' => $optional(data_get($input, 'utm_campaign'), ['required', 'string', 'max:255', 'min:2']),
            'utm_term' => $optional(data_get($input, 'utm_term'), ['required', 'string', 'max:255', 'min:2']),
            'utm_content' => $optional(data_get($input, 'utm_content'), ['required', 'string', 'max:255', 'min:2']),
            'tags' => ['array'],
            'external_id' => $optional(data_get($input, 'external_id'), [
                'nullable', 'string', 'max:255', 'min:2',
                Rule::unique('links')->where('workspace_id', $workspace?->id)->ignore($ignoreId),
            ]),
            'password' => $optional(data_get($input, 'password'), ['required', 'string', 'max:255', 'min:6']),
            'expires_at' => Rule::when(
                fn () => filled(data_get($input, 'expires_at')) || filled(data_get($input, 'expired_redirect_url')),
                ['required', 'date'],
            ),
            'expired_redirect_url' => $optional(data_get($input, 'expired_redirect_url'), ['nullable', 'url', 'max:255', 'min:2']),
        ];
    }
}
