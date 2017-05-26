<?php

namespace Tests\Unit;

use App\Events\SshLoginAttempted;
use App\Services\SshClient;
use Illuminate\Support\Facades\Event;
use Mockery;
use phpseclib\Net\SSH2;
use Tests\Mocks\SshMock;
use Tests\TestCase;
use Tests\TestHelper;

class SShClientTest extends TestCase
{
    use TestHelper;

    public function atestRunForASingleGoodCommand()
    {
        $client = Mockery::mock('App\Services\SshClient[connect, attemptLogin, processCommand, CheckOutputStatus]', [$this->getRsaMock()])->shouldAllowMockingProtectedMethods()
        ->shouldReceive('connect')->withAnyArgs()->andReturnNull()
        ->shouldReceive('attemptLogin')->withArgs(['server'])->andReturnNull()
        ->shouldReceive('processCommand')->withArgs(['foo'])->andReturn('foo')
        ->shouldReceive('checkOutputStatus')->withAnyArgs()->andReturnValues([0])
        ->getMock();

        $this->setProperty($client, 'ssh', new SshMock());
        $output = $client->run('foo');
        $this->assertEquals($output, 'foo');

    }

    public function testRunForAnArrayOfGoodCommands()
    {
        $client = Mockery::mock('App\Services\SshClient[connect, attemptLogin, processCommand, CheckOutputStatus]', [$this->getRsaMock()])->shouldAllowMockingProtectedMethods()
            ->shouldReceive('connect')->withAnyArgs()->andReturnNull()
            ->shouldReceive('attemptLogin')->withArgs(['server'])->andReturnNull()
            ->shouldReceive('processCommand')->withArgs(['foo'])->andReturn('foo')
            ->shouldReceive('checkOutputStatus')->withAnyArgs()->andReturnValues([0])
            ->getMock();

        $this->setProperty($client, 'ssh', new SshMock());
        $output = $client->run(['foo', 'foo']);
        $this->assertArraySubset(['foo', 'foo'], $output);
        $this->assertCount(2, $output);

    }

    public function testAttemptLoginWithSuccessAttempt()
    {
        $client = new SshClient($this->getRsaMock());
        $this->setProperty($client, 'ssh', new SshMock());
        Event::fake();
        $server = $this->createServer();
        $this->callProtectedMethod($client, 'attemptLogin', [$server]);
        Event::assertDispatched(SshLoginAttempted::class, function ($event) use ($server) {
            return data_get($event->server, 'id') === data_get($server, 'id') && $event->state;
        });

    }

    public function testCreateSshKey()
    {
        $client = new SshClient($this->getRsaMock());
        $this->setProperty($client, 'ssh', new SshMock());
        $key = $client->createSsHKey();
        $this->assertEquals($key, '123');
    }

    public function ()
    {
        
    }




}
