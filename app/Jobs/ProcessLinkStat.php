<?php

declare(strict_types=1);

namespace App\Jobs;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

use App\Services\UserAgentService;

use App\Enums\LinkStat\Event;

use App\Models\Link;
use App\Models\LinkStat;

class ProcessLinkStat implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new job instance.
     */
    public function __construct(
        public Link $link,
        public string $userAgent,
        public array $languages,
        public string $ip,
        public bool $qr,
        public array $utms,
        public ?string $referer
    ) {}

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $service = new UserAgentService();

        // user geo
        $geo = geoip($this->ip);

        LinkStat::create([
            'workspace_id' => $this->link->workspace_id,
            'link_id' => $this->link->id,
            'event' => $this->qr ? Event::QR_SCAN : Event::CLICK,
            'country' => $geo->iso_code,
            'region' => $geo->state_name,
            'city' => $geo->city,
            'browser' => $service->getBrowser($this->userAgent),
            'os' => $service->getOS($this->userAgent),
            'device' => $service->getDevice($this->userAgent),
            'language' => $service->getLanguage($this->languages),
            'referer' => $service->getReferer($this->referer),
            'utm_medium' => $this->utms['utm_medium'],
            'utm_source' => $this->utms['utm_source'],
            'utm_campaign' => $this->utms['utm_campaign'],
            'utm_content' => $this->utms['utm_content'],
            'utm_term' => $this->utms['utm_term'],
            'ip' => $this->ip,
        ]);

        $this->link->update([
            'clicks' => $this->link->clicks + 1,
            'last_click' => now(),
        ]);
    }
}
