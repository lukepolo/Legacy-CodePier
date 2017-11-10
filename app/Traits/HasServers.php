<?php

namespace App\Traits;

use App\Services\Systems\SystemService;

trait HasServers
{
    public function isLoadBalanced()
    {
        return $this->filterServersByType(SystemService::LOAD_BALANCER, false)->count() ? true : false;
    }

    public function hasWorkerServers()
    {
        return $this->filterServersByType(SystemService::WORKER_SERVER, false)->count() ? true : false;
    }

    public function hasDatabaseServers()
    {
        return $this->filterServersByType(SystemService::DATABASE_SERVER, false)->count() ? true : false;
    }

    public function hasFullStackServers()
    {
        return $this->filterServersByType(SystemService::FULL_STACK_SERVER, false)->count() ? true : false;
    }

    public function hasWebServers()
    {
        return $this->filterServersByType(SystemService::WEB_SERVER, false)->count() ? true : false;
    }

    public function filterServersByType($types, $provisionedOnly = true)
    {
        $types = collect($types);

        return $this->servers->filter(function ($server) use ($types, $provisionedOnly) {
            if ($provisionedOnly && $server->progress < 100) {
                return false;
            }

            return $types->contains($server->type);
        });
    }

    public function excludeServersByType($types, $provisionedOnly = true)
    {
        $types = collect($types);

        return $this->servers->filter(function ($server) use ($types, $provisionedOnly) {
            if ($provisionedOnly && $server->progress < 100) {
                return false;
            }

            return ! $types->contains($server->type);
        });
    }

    public function hasServer($server)
    {
        $servers = $this->servers;

        return ! empty($servers) && $servers->count() && $servers->pluck('id')->contains($server->id);
    }
}
