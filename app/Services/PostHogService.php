<?php

declare(strict_types=1);

namespace App\Services;

use App\Jobs\PostHog\SendEvent;
use App\Models\Workspace;
use Illuminate\Support\Facades\Log;
use Throwable;

class PostHogService
{
    public static function isEnabled(): bool
    {
        return (bool) config('services.posthog.enabled')
            && (bool) config('services.posthog.api_key');
    }

    /**
     * Gate for call sites that pre-check before ever reaching capture()/
     * identify() — e.g. to skip dispatching a job at all when disabled.
     * Also true locally so that path still logs (via capture()'s own
     * logLocally()) even without a real API key.
     */
    public static function shouldTrack(): bool
    {
        return self::isEnabled() || app()->environment('local');
    }

    /**
     * @param  array<string, mixed>  $properties
     */
    public function capture(string $distinctId, string $event, array $properties = [], ?Workspace $workspace = null): void
    {
        try {
            $payload = [
                'distinctId' => $distinctId,
                'event' => $event,
                'properties' => $properties,
            ];

            if ($workspace) {
                $payload['properties']['$groups'] = ['workspace' => (string) $workspace->id];
                $payload['properties']['workspace_id'] = (string) $workspace->id;
                $payload['properties']['plan'] = $workspace->plan?->name;
            }

            $this->logLocally('capture', $payload);

            if (self::isEnabled()) {
                $this->dispatch('capture', $payload);
            }
        } catch (Throwable $e) {
            Log::warning('PostHogService: failed to capture event', [
                'event' => $event,
                'error' => $e->getMessage(),
            ]);
        }
    }

    /**
     * @param  array<string, mixed>  $properties
     */
    public function identify(string $distinctId, array $properties = []): void
    {
        try {
            $payload = [
                'distinctId' => $distinctId,
                'properties' => $properties,
            ];

            $this->logLocally('identify', $payload);

            if (self::isEnabled()) {
                $this->dispatch('identify', $payload);
            }
        } catch (Throwable $e) {
            Log::warning('PostHogService: failed to identify', [
                'error' => $e->getMessage(),
            ]);
        }
    }

    /**
     * @param  array<string, mixed>  $properties
     */
    public function groupIdentify(string $groupType, string $groupKey, array $properties = []): void
    {
        try {
            $payload = [
                'groupType' => $groupType,
                'groupKey' => $groupKey,
                'properties' => $properties,
            ];

            $this->logLocally('groupIdentify', $payload);

            if (self::isEnabled()) {
                $this->dispatch('groupIdentify', $payload);
            }
        } catch (Throwable $e) {
            Log::warning('PostHogService: failed to group identify', [
                'error' => $e->getMessage(),
            ]);
        }
    }

    /**
     * Local-only visibility into what would be sent, so events can be verified
     * from laravel.log without a real API key configured.
     *
     * @param  array<string, mixed>  $payload
     */
    private function logLocally(string $method, array $payload): void
    {
        if (! app()->environment('local')) {
            return;
        }

        Log::info("PostHogService: {$method}", $payload);
    }

    /**
     * @param  array<string, mixed>  $payload
     */
    private function dispatch(string $method, array $payload): void
    {
        try {
            SendEvent::dispatch($method, $payload);
        } catch (Throwable $e) {
            Log::warning('PostHogService: failed to dispatch event', ['method' => $method, 'error' => $e->getMessage()]);
        }
    }
}
