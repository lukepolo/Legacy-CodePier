<?php

namespace App\Traits;

use App\Classes\FailedRemoteResponse;
use App\Classes\SuccessRemoteResponse;
use App\Exceptions\FailedCommand;
use App\Exceptions\SshConnectionFailed;
use Closure;

trait ServerCommandTrait
{
    private $error = false;
    private $remoteErrors;
    private $remoteSuccesses;

    /**
     * Runs a command on a external server.
     *
     * @param Closure $function
     * @throws \Exception
     */
    public function runOnServer(Closure $function)
    {
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
    }

    /**
     * Checks to see if the command was successful.
     * @return bool
     */
    public function wasSuccessful()
    {
        return ! $this->error;
    }

    /**
     * Gets the remote response to return.
     *
     * @return \Illuminate\Http\JsonResponse
     */
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
