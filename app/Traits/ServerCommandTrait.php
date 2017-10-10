<?php

namespace App\Traits;

use Closure;
use App\Models\Command;
use App\Models\Server\Server;
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
     * This must have a connected `server_id` attribute.
     *
     * @param Server $server
     * @param Model $model
     * @param Command $command
     * @param null $status
     * @return ServerCommand
     */
    public function makeCommand(Server $server, Model $model, Command $command = null, $status = null)
    {
        $hasCommand = ! empty($command);
        $description = method_exists($model, 'commandDescription') ? $model->commandDescription($status) : $status;

        if (empty($command)) {
            if (empty($status)) {
                \Log::critical('missing status for '.get_class($model));
            }

            $command = Command::create([
                'server_id' => $server->id,
                'commandable_id' => $model->id,
                'commandable_type' => get_class($model),
                'status' => 'Queued',
                'description' => $description,
            ]);
        }

        $this->serverCommand = ServerCommand::create([
            'server_id' => $server->id,
            'command_id' => $command->id,
            'description' => $hasCommand ? $description : null,
        ]);

        return $this->serverCommand;
    }

    /**
     * Runs a command on a external server.
     *
     * @param Closure $function
     * @param ServerCommand $serverCommand
     * @param bool $debug
     * @throws \Exception
     */
    public function runOnServer(Closure $function, ServerCommand $serverCommand = null, $debug = true)
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
                $this->updateServerCommand(microtime(true) - $start, $this->remoteSuccesses);
            }
        } catch (\Exception $e) {
            switch (get_class($e)) {
                case SshConnectionFailed::class:
                case FailedCommand::class:
                    $message = $e->getMessage();
                    break;
                default:
                    if (config('app.debug') && $debug) {
                        throw $e;
                    }
                    $message = 'We had a system error please contact support.';
                    break;
            }

            $this->remoteErrors[] = new FailedRemoteResponse($e, $message);

            if (count($this->remoteErrors)) {
                $this->error = true;

                if (! empty($this->serverCommand)) {
                    $this->updateServerCommand(microtime(true) - $start, $this->remoteErrors, false);
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

    /**
     * Updates the server command.
     * @param $runtime
     * @param $log
     * @param $completed
     */
    public function updateServerCommand($runtime, $log, $completed = true)
    {
        $this->serverCommand->update([
            'runtime' => $runtime,
            'log' =>  $log,
            'completed' => $completed,
            'failed' => ! $completed,
        ]);
    }
}
