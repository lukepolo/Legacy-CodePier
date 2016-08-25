<?php

namespace App\Classes;

use App\Models\Server;

/**
 * Class FailedRemoteResponse
 *
 * @package App\Classes
 */
class FailedRemoteResponse
{
    public $log;
    public $server;
    public $message;

    /**
     * FailedRemoteResponse constructor.
     * @param Server $server
     * @param \Exception $exception
     * @param null $message
     */
    public function __construct(Server $server, \Exception $exception, $message = null)
    {
        $this->log = $exception->getMessage();
        $this->server = $server;
        $this->message = $message ?: $exception->getMessage();
    }
}
