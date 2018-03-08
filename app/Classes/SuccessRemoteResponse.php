<?php

namespace App\Classes;

class SuccessRemoteResponse
{
    public $message;

    /**
     * FailedServerResponse constructor.
     *
     * @param $log
     */
    public function __construct($log)
    {
        $this->message = $log;
    }
}
