<?php

namespace App\Classes;

/**
 * Class FailedRemoteResponse.
 */
class FailedRemoteResponse
{
    public $log;
    public $server;
    public $message;

    /**
     * FailedRemoteResponse constructor.
     *
     * @param \Exception $exception
     * @param null       $message
     */
    public function __construct(\Exception $exception, $message = null)
    {
        $this->log = $exception->getMessage();
        $this->message = $message ?: $exception->getMessage();
    }
}
