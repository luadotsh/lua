<?php

declare(strict_types=1);

namespace App\Observers;

use App\Models\Domain;
use Illuminate\Support\Facades\Redis;

class DomainObserver
{
    /**
     * The Redis instance.
     */
    protected $redis;

    public function __construct()
    {
        $this->redis = Redis::connection('default');
    }

    /**
     * Handle the Domain "created" event.
     */
    public function created(Domain $domain): void
    {
        $this->redis->set($domain->domain, 'lua.sha');

    }

    /**
     * Handle the Domain "updated" event.
     */
    public function updated(Domain $domain): void
    {
        // delete the old domain
        $this->redis->del($domain->getOriginal('domain'));

        // set the new domain
        $this->redis->set($domain->domain, 'lua.sha');
    }

    /**
     * Handle the Domain "deleted" event.
     */
    public function deleted(Domain $domain): void
    {
        $this->redis->del($domain->domain);
    }
}
