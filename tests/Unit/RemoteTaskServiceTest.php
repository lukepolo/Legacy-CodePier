<?php

namespace Tests\Unit;

use App\Events\SshLoginAttempted;
use App\Services\RemoteTaskService;
use Illuminate\Support\Facades\Event;
use Mockery;
use Tests\Mocks\SshMock;
use Tests\TestCase;
use Tests\TestHelper;


class RemoteTaskServiceTest extends TestCase
{
    use TestHelper;

    public function testRunForASingleGoodCommand()
    {
        $client = Mockery::mock('App\Services\RemoteTaskService[connect, attemptLogin, processCommand, CheckOutputStatus]', [$this->getRsaMock()])->shouldAllowMockingProtectedMethods()
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
        $client = Mockery::mock('App\Services\RemoteTaskService[connect, attemptLogin, processCommand, CheckOutputStatus]', [$this->getRsaMock()])->shouldAllowMockingProtectedMethods()
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
        $client = new RemoteTaskService($this->getRsaMock());
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
        $client = new RemoteTaskService($this->getRsaMock());
        $this->setProperty($client, 'ssh', new SshMock());
        $key = $client->createSsHKey();
        $this->assertEquals($key, '123');
    }

}
