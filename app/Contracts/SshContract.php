<?php

namespace App\Contracts;


interface SshContract
{
    public function run($command, $read = false, ?$expectedFailure = false): string;

    public function connect($server): void;

    public function for(string $user): self;


}