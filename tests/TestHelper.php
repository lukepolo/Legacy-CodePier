<?php

namespace Tests;

use Mockery;
use phpseclib\Crypt\RSA;
use App\Models\Server\Server;

trait TestHelper
{
    public function createServer(int $count = 1, array $attributes = [])
    {
        return factory(Server::class, $count)->make();
    }

    public function getRsaMock()
    {
        $rsaMock = Mockery::mock(Rsa::class);

        return $rsaMock->shouldReceive('loadKey')->withAnyArgs()->andReturnNull()
            ->shouldReceive('setPublicKeyFormat')->withAnyArgs()->andReturnNull()
            ->shouldReceive('createKey')->withNoArgs()->andReturn('123')
            ->getMock();
    }

    public function setProperty($object, $property, $value)
    {
        $rfc = new \ReflectionClass($object);
        $prop = $rfc->getProperty($property);
        $prop->setAccessible(true);
        $prop->setValue($object, $value);
    }

    public function callProtectedMethod($object, $methodName, $args = [])
    {
        $rfc = new \ReflectionClass($object);
        $method = $rfc->getMethod($methodName);
        $method->setAccessible(true);

        return $method->invokeArgs($object, $args);
    }
}
