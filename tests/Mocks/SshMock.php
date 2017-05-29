<?php
/**
 * Created by PhpStorm.
 * User: dpc
 * Date: 17/5/17
 * Time: 12:22 AM.
 */

namespace Tests\Mocks;

class SshMock
{
    public function setTimeout(int $integer): void
    {
    }

    public function exec(string $command, $callback = null): string
    {
        return $command;
    }

    public function login($user, $rsa): bool
    {
        return true;
    }

    public function getExitStatus()
    {
        return 0;
    }
}
