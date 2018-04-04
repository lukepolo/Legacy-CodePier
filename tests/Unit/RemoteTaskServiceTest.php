<?php

namespace Tests\Unit;

use Tests\TestCase;
use Tests\InteractsWithServers;

class RemoteTaskServiceTest extends TestCase
{
    use InteractsWithServers;

    public function testCanConnect()
    {
        $this->assertTrue($this->connect());
    }

    public function testCanRunCommandAndReceiveOutput()
    {
        $this->connect();

        $this->assertEquals($this->remoteTaskService->run('echo foobar'), 'foobar');
    }

    public function testFileHasLine()
    {
        $this->connect();
        $file = '/opt/codepier/'.str_random();
        $lines = [
            "We're no strangers to love",
            'You know the rules and so do I',
            "A full commitment's what I'm thinking of"
        ];

        $this->remoteTaskService->writeToFile($file, implode("\n", $lines));

        $this->assertTrue($this->remoteTaskService->doesFileHaveLine($file, 'You know the rules and so do I'));
    }

    public function testFileDoesNotHaveLine()
    {
        $this->connect();
        $file = '/opt/codepier/'.str_random();
        $lines = [
            "We're no strangers to love",
            'You know the rules and so do I',
            "A full commitment's what I'm thinking of"
        ];

        $this->remoteTaskService->writeToFile($file, implode("\n", $lines));

        $this->assertFalse($this->remoteTaskService->doesFileHaveLine($file, 'Sweet dreams are made of this'));
    }

    public function testCanGetFullLineByPartialString()
    {
        $this->connect();
        $file = '/opt/codepier/'.str_random();
        $lines = [
            "We're no strangers to love",
            'You know the rules and so do I',
            "A full commitment's what I'm thinking of"
        ];
        
        $this->remoteTaskService->writeToFile($file, implode("\n", $lines));

        $this->assertEquals($this->remoteTaskService->getFileLine($file, 'You know the rules'), 'You know the rules and so do I');
    }

    public function testCanWriteToFile()
    {
        $this->connect();
        $file = '/opt/codepier/'.str_random();

        $this->remoteTaskService->writeToFile($file, 'foobar');

        $this->assertEquals($this->remoteTaskService->getFileContents($file), 'foobar');
    }

    public function testCanAppendTextToFile()
    {
        $this->connect();
        $file = '/opt/codepier/'.str_random();

        $this->remoteTaskService->writeToFile($file, "We're no strangers to love");
        $this->remoteTaskService->appendTextToFile($file, 'You know the rules and so do I');

        $this->assertTrue($this->remoteTaskService->doesFileHaveLine($file, "We're no strangers to love"));
        $this->assertTrue($this->remoteTaskService->doesFileHaveLine($file, 'You know the rules and so do I'));
    }

    public function testCanFindTextAndAppend()
    {
        $this->connect();
        $file = '/opt/codepier/'.str_random();

        $this->remoteTaskService->writeToFile($file, 'You know the rules');
        $this->remoteTaskService->findTextAndAppend($file, 'You know the rules', 'and so do I');
        
        $this->assertEquals($this->remoteTaskService->getFileContents($file), "You know the rules\nand so do I");
    }

    public function testCanRemoveLine()
    {
        $this->connect();
        $file = '/opt/codepier/'.str_random();

        $this->remoteTaskService->writeToFile($file, 'You know the rules');
        $this->remoteTaskService->removeLineByText($file, 'You know the rules');

        $this->assertNotEquals($this->remoteTaskService->getFileContents($file), 'You know the rules');
    }

    public function testCanRemoveDirectory()
    {
        $this->connect();
        $directory = '/opt/codepier/'.str_random();

        $this->remoteTaskService->makeDirectory($directory);
        $this->remoteTaskService->removeDirectory($directory);

        $this->assertFalse($this->remoteTaskService->hasDirectory($directory));
    }

    public function testCanRemoveFile()
    {
        $this->connect();
        $file = '/opt/codepier/'.str_random();

        $this->remoteTaskService->writeToFile($file, null);
        $this->remoteTaskService->removeFile($file);

        $this->assertFalse($this->remoteTaskService->hasFile($file));
    }

    public function testCanUpdateTextWithDoubleQuotes()
    {
        $this->connect();
        $file = '/opt/codepier/'.str_random();

        $this->remoteTaskService->writeToFile($file, "We're no strangers to love");
        $this->remoteTaskService->updateText($file, "We're no strangers to love", 'Sweet dreams are made of this');

        $this->assertNotEquals($this->remoteTaskService->getFileContents($file), "We're no strangers to love");
        $this->assertEquals($this->remoteTaskService->getFileContents($file), 'Sweet dreams are made of this');
    }

    public function testCanUpdateTextWithSingleQuotes()
    {
        $this->connect();
        $file = '/opt/codepier/'.str_random();

        $this->remoteTaskService->writeToFile($file, 'Sweet dreams are made of this');
        $this->remoteTaskService->updateText($file, 'Sweet dreams are made of this', 'Who am I to disagree?');

        $this->assertNotEquals($this->remoteTaskService->getFileContents($file), 'Sweet dreams are made of this');
        $this->assertEquals($this->remoteTaskService->getFileContents($file), 'Who am I to disagree?');
    }

    public function testFileExists()
    {
        $this->connect();
        $file = '/opt/codepier/'.str_random();

        $this->remoteTaskService->writeToFile($file, 'foobar');

        $this->assertTrue($this->remoteTaskService->hasFile($file));
    }

    public function testFileDoesNotExist()
    {
        $this->connect();

        $this->assertFalse($this->remoteTaskService->hasFile('/opt/codepier/some-dummy-nonexistent-file'));
    }

    public function testFileIsEmpty()
    {
        $this->connect();
        $file = '/opt/codepier/'.str_random();

        $this->remoteTaskService->createFile($file);

        $this->assertTrue($this->remoteTaskService->isFileEmpty($file));
    }

    public function testFileIsNotEmpty()
    {
        $this->connect();
        $file = '/opt/codepier/'.str_random();

        $this->remoteTaskService->writeToFile($file, 'foobar');

        $this->assertFalse($this->remoteTaskService->isFileEmpty($file));
    }

    public function testCanGetFileContents()
    {
        $this->connect();
        $file = '/opt/codepier/'.str_random();

        $this->remoteTaskService->writeToFile($file, 'foobar');

        $this->assertEquals($this->remoteTaskService->getFileContents($file), 'foobar');
    }

    public function testDirectoryExists()
    {
        $this->connect();
        $directory = '/opt/codepier/'.str_random();

        $this->remoteTaskService->makeDirectory($directory);

        $this->assertTrue($this->remoteTaskService->hasDirectory($directory));
    }

    public function testCommandExists()
    {
        $this->connect();

        $this->assertTrue($this->remoteTaskService->hasCommand('echo'));
    }

    public function testCommandDoesNotExist()
    {
        $this->connect();

        $this->assertFalse($this->remoteTaskService->hasCommand('foobar'));
    }


    public function testCanAddSshKey()
    {
        $this->connect();
        $key = $this->remoteTaskService->createSshKey();
        $title = str_random();
        
        $this->remoteTaskService->addSshKey($title, $key['publickey'], $key['privatekey']);

        $this->assertEquals(substr($this->remoteTaskService->getFileContents('/home/codepier/.ssh/'.$title.'_id_rsa.pub'), 0, 7), 'ssh-rsa');
        $this->assertEquals(substr($this->remoteTaskService->getFileContents('/home/codepier/.ssh/'.$title.'_id_rsa'), 0, 31), '-----BEGIN RSA PRIVATE KEY-----');
        $this->assertContains('IdentityFile '.'/home/codepier/.ssh/'.$title.'_id_rsa', $this->remoteTaskService->getFileContents('/home/codepier/.ssh/config'));
    }

    public function testCanCreateSshKey()
    {
        $remoteTaskService = app()->make('App\Services\RemoteTaskService');

        $key = $remoteTaskService->createSshKey();

        $this->assertEquals(substr($key['publickey'], 0, 7), 'ssh-rsa');
        $this->assertEquals(substr($key['privatekey'], 0, 31), '-----BEGIN RSA PRIVATE KEY-----');
    }
}
