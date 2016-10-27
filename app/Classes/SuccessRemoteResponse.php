<?php

namespace App\Classes;

use App\Models\Server\Server;

/**
 * Class SuccessRemoteResponse.
 */
class SuccessRemoteResponse
{
    public $log;
    public $server;

    /**
     * FailedServerResponse constructor.
     *
     * @param Server $server
     * @param $log
     */
    public function __construct(Server $server, $log)
    {
        $this->log = $log;
        $this->server = $server;
    }
}
