<?php

namespace App\Contracts;


use App\Models\Server\Server;
use Illuminate\Database\Eloquent\Collection;

interface SshContract
{
    public function run($command, $read = false, $expectedFailure = false);

    public function connect(Collection $server): void;

    public function for(string $user): self;


}