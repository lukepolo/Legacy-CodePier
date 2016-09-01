<?php

namespace App\Exceptions\Traits;

use App\Classes\FailedRemoteResponse;
use App\Classes\SuccessRemoteResponse;
use App\Exceptions\FailedCommand;
use App\Exceptions\SshConnectionFailed;
use App\Models\Server;
use Closure;

/**
 * Class ServerErrorTrait.
 */
trait ServerErrorTrait
{
    public $remoteErrors;
    public $remoteSuccesses;
    public $error = false;

    public function runOnServer(Server $server, Closure $function, $throwErrors = false)
    {
        $this->error = false;

        try {
            $remoteResponse = new SuccessRemoteResponse($server, $function());

            $this->remoteSuccesses[] = $remoteResponse;

            return $remoteResponse;
        } catch (\Exception $e) {
            switch (get_class($e)) {
                case SshConnectionFailed::class:
                case FailedCommand::class:
                    $message = $e->getMessage();

                    $server->ssh_connection = false;
                    $server->save();
                    break;
                default:
                    throw new \Exception($e->getMessage());
                    break;
            }

            $remoteResponse = new FailedRemoteResponse($server, $e, $message);

            $this->remoteErrors[] = $remoteResponse;

            if (count($this->remoteErrors)) {
                $this->error = true;
            }

            return $remoteResponse;
        }
    }

    public function successful()
    {
        return !$this->error;
    }

    public function remoteResponse($errors = true)
    {
        if (count($this->remoteErrors)) {
            return response()->json([
                'errors' => $this->remoteErrors,
            ]);
        }

        return response()->json();
    }
}
