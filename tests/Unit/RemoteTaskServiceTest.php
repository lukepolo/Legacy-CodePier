<?php

namespace Tests\Unit;

use App\Contracts\RemoteTaskServiceContract;
use App\Services\RemoteTaskService;
use App\Services\SshClient;
use Mockery;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestHelper;

class RemoteTaskServiceTest extends TestCase
{
    use TestHelper;

    public function testIfRunRunsACommand()
    {
        $client = Mockery::mock(SshClient::class, [$this->getRsaMock()])->shouldReceive('run')->withArgs(['foo'])->andReturn('foo')->getMock();
        $service = new RemoteTaskService($client);
        $mockedService = Mockery::mock('App\Services\RemoteTaskService[run]', [$client]);
        $output = $service->run('foo');
        $this->assertEquals($output, 'foo');

    }

}