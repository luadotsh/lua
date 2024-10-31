<?php

declare(strict_types=1);

namespace App\Observers;

use Illuminate\Support\Facades\Redis;

use App\Models\Domain;

class DomainObserver
{
    /**
     * The Redis instance.
     */
    protected $redis;

    public function __construct() {
        $this->redis = Redis::connection('default');
    }

    /**
     * Handle the Domain "created" event.
     */
    public function created(Domain $domain): void
    {
        $this->redis->set($domain->domain, "lua.sha");

    }

    /**
     * Handle the Domain "updated" event.
     */
    public function updated(Domain $domain): void
    {
        // delete the old domain
        $this->redis->del($domain->getOriginal('domain'));

        // set the new domain
        $this->redis->set($domain->domain, "lua.sha");
    }

    /**
     * Handle the Domain "deleted" event.
     */
    public function deleted(Domain $domain): void
    {
        $this->redis->del($domain->domain);
    }

    /**
     * Handle the Domain "restored" event.
     */
    public function restored(Domain $domain): void
    {
        //
    }

    /**
     * Handle the Domain "force deleted" event.
     */
    public function forceDeleted(Domain $domain): void
    {
        //
    }
}
