<?php

namespace App\Exceptions\Traits;

use App\Classes\FailedRemoteResponse;
use App\Classes\SuccessRemoteResponse;
use App\Exceptions\FailedCommand;
use App\Exceptions\SshConnectionFailed;
use Closure;

/**
 * Class ServerErrorTrait.
 */
trait ServerErrorTrait
{
    private $remoteErrors;
    private $remoteSuccesses;

    public function runOnServer(Closure $function)
    {
        $this->error = false;

        try {
            $remoteResponse = new SuccessRemoteResponse($function());

            $this->remoteSuccesses[] = $remoteResponse;
        } catch (\Exception $e) {
            switch (get_class($e)) {
                case SshConnectionFailed::class:
                case FailedCommand::class:
                    $message = $e->getMessage();
                    break;
                default:
                    throw new \Exception($e->getMessage());
                    break;
            }

            $remoteResponse = new FailedRemoteResponse($e, $message);

            $this->remoteErrors[] = $remoteResponse;

            if (count($this->remoteErrors)) {
                $this->error = true;
            }
        }

        return $this->remoteResponse();
    }

    public function remoteResponse()
    {
        if (count($this->remoteErrors)) {
            return response()->json([
                'errors' => $this->remoteErrors,
            ]);
        }

        return response()->json();
    }
}
