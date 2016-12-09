<?php

namespace App\Traits;

use Closure;
use App\Models\Command;
use App\Models\ServerCommand;
use App\Exceptions\FailedCommand;
use App\Classes\FailedRemoteResponse;
use App\Classes\SuccessRemoteResponse;
use App\Exceptions\SshConnectionFailed;
use Illuminate\Database\Eloquent\Model;

trait ServerCommandTrait
{
    private $error = false;
    private $remoteErrors;
    private $serverCommand;
    private $remoteSuccesses;

    /**
     * @param Model $model
     * @param null $siteId
     */
    public function makeCommand(Model $model, $siteId = null)
    {
        $command = null;

        if (empty($siteId)) {
            $hiddenAttributes = $model->getHidden();

            if (isset($hiddenAttributes['command'])) {
                $command = $hiddenAttributes['command'];
            }
        } else {
            $command = Command::create([
                'site_id' => $siteId,
                'commandable_id' => $model->id,
                'commandable_type' => get_class($model),
            ]);
        }

        if (! empty($command)) {
            $this->serverCommand = ServerCommand::create([
                'server_id' => $model->server_id,
                'command_id' => $command->id,
            ]);
        }
    }

    /**
     * Runs a command on a external server.
     *
     * @param Closure $function
     * @param ServerCommand $serverCommand
     * @throws \Exception
     */
    public function runOnServer(Closure $function, ServerCommand $serverCommand = null)
    {
        if (! empty($serverCommand)) {
            $this->serverCommand = $serverCommand;
        }

        $start = microtime(true);

        try {
            if (! empty($this->serverCommand)) {
                $this->serverCommand->update([
                    'started' => true,
                ]);
            }

            $remoteResponse = new SuccessRemoteResponse($function());

            $this->remoteSuccesses[] = $remoteResponse;

            if (! empty($this->serverCommand)) {
                $this->serverCommand->update([
                    'runtime' => microtime(true) - $start,
                    'log' =>  $this->remoteSuccesses,
                    'completed' => true,
                ]);
            }
        } catch (\Exception $e) {
            switch (get_class($e)) {
                case SshConnectionFailed::class:
                case FailedCommand::class:
                    $message = $e->getMessage();
                    break;
                default:
                    throw $e;
                    break;
            }

            $this->remoteErrors[] = new FailedRemoteResponse($e, $message);

            if (count($this->remoteErrors)) {
                $this->error = true;

                if (! empty($this->serverCommand)) {
                    $this->serverCommand->update([
                        'runtime' => microtime(true) - $start,
                        'log' => $this->remoteErrors,
                        'failed' => true,
                    ]);
                }
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

    /**
     * Gets the errors from the command.
     *
     * @return mixed
     */
    private function getCommandErrors()
    {
        return json_encode($this->remoteErrors);
    }
}
