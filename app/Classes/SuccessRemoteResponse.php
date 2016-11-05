<?php

namespace App\Classes;

class SuccessRemoteResponse
{
    public $log;

    /**
     * FailedServerResponse constructor.
     *
     * @param $log
     */
    public function __construct($log)
    {
        $this->log = $log;
    }
}
