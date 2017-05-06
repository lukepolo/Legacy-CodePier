<?php

namespace App\Services\Systems\Ubuntu\V_16_04\Languages\PHP;

use App\Models\Server\Server;

class PHPSettings
{
    /**
     * @description Max File Upload in Megabytes (MB)
     *
     * @params megabytes
     */
    public function uploadSize(Server $server, $data)
    {
    }

    /**
     * @description Manually optimize OPCache
     */
    public function OpCache()
    {
    }
}
