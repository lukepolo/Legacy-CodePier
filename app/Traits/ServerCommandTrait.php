<?php

namespace App\Traits;

use App\Classes\FailedRemoteResponse;
use App\Classes\SuccessRemoteResponse;
use App\Exceptions\FailedCommand;
use App\Exceptions\SshConnectionFailed;
use App\Models\Command;
use Closure;
use Illuminate\Database\Eloquent\Model;

trait ServerCommandTrait
{
    private $error = false;
    private $remoteErrors;
    private $remoteSuccesses;
    private $command;

    /**
     * @param Model $model
     * @return Command
     */
    public function makeCommand(Model $model)
    {
        $this->command = Command::create([
            'type' => get_class($model),
            'server_id' => $model->server_id,
        ]);

        $model->commands()->attach($this->command);

        return $this->command;
    }

    /**
     * Runs a command on a external server.
     *
     * @param Closure $function
     * @param Command $command
     * @throws \Exception
     */
    public function runOnServer(Closure $function, Command $command = null)
    {
        $start = microtime(true);

        try {

            $this->command->update([
                'started' => true,
            ]);

            $remoteResponse = new SuccessRemoteResponse($function());

            $this->remoteSuccesses[] = $remoteResponse;

            if(!empty($command)) {
                $this->command->update([
                    'runtime' => microtime(true) - $start,
                    'log' =>  $this->remoteSuccesses,
                    'completed' => true
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

            $this->remoteErrors[] =  new FailedRemoteResponse($e, $message);

            if (count($this->remoteErrors)) {
                $this->error = true;

                if(!empty($command)) {
                    $this->command->update([
                        'runtime' => microtime(true) - $start,
                        'log' => $this->remoteErrors,
                        'failed' => true
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
}
