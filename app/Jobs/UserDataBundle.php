<?php

namespace App\Jobs;

use App\Mail\UserBundleEmail;
use Illuminate\Support\Facades\Mail;
use ZipArchive;
use League\Csv\Writer;
use App\Models\Schema;
use App\Models\User\User;
use Illuminate\Bus\Queueable;
use Illuminate\Support\Facades\File;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class UserDataBundle implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private $user;
    private $directory;

    /**
     * Create a new job instance.
     *
     * @param User $user
     */
    public function __construct(User $user)
    {
        $this->user = $user;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $this->user->load([
            'sites',
            'sites.files',
            'sites.sshKeys',
            'sites.schemas',
            'sites.schemaUsers',
            'sites.environmentVariables',
            'sites.userRepositoryProvider.repositoryProvider',

            'servers',
            'servers.serverProvider',
            'servers.sshKeys',
            'servers.files',
            'servers.schemas',
            'servers.schemaUsers',
            'servers.environmentVariables',
            'servers.backups',

            'sshKeys',
            'userLoginProvider',
            'userServerProviders.serverProvider',
            'userRepositoryProviders.repositoryProvider',
            'userNotificationProviders.notificationProvider',
        ]);

        $this->directory = storage_path("gdpr/bundles/{$this->user->id}");
        $bundlePath = storage_path("gdpr/bundles/{$this->user->name}'s-data.zip");

        if (! File::isDirectory($this->directory)) {
            File::makeDirectory($this->directory, 0775);
        }

        $this->userData();
        $this->sites();
        $this->servers();

        $zip = new ZipArchive;
        if ($zip->open($bundlePath, ZipArchive::CREATE) === true) {
            foreach (File::allFiles($this->directory) as $file) {
                if ($file->isFile()) {
                    $zip->addFile($file->getPathname(), $file->getFilename());
                }
            }

            $zip->close();
        }

        File::deleteDirectory($this->directory);
        Mail::to($this->user->email)
            ->send(new UserBundleEmail($bundlePath));
    }

    /*
    |--------------------------------------------------------------------------
    | USER DATA
    |--------------------------------------------------------------------------
    */

    private function userData()
    {
        $this->createCSV('user', [
            'name',
            'email',
        ])->insertOne([
            'name' => $this->user->name,
            'email' => $this->user->email,
        ]);

        $this->userSshKeys();
        $this->userLoginProvider();
        $this->userServerProviders();
        $this->userRepositoryProviders();
        $this->userNotificationProviders();
    }

    private function userSshKeys()
    {
        $csv = $this->createCSV('user-ssh-keys', [
            'key name',
            'ssh key'
        ]);

        foreach ($this->user->sshKeys as $sshKey) {
            $csv->insertOne([
                'key name' => $sshKey->name,
                'ssh key' => $sshKey->ssh_key
            ]);
        }
    }
    
    private function userLoginProvider()
    {
        $provider = $this->user->userLoginProvider;
        if ($provider) {
            $this->createCSV('login-providers', [
                'provider',
                'label',
                'id'
            ])->insertOne([
                'provider' => $provider->provider,
                'label' => $provider->provider,
                'id' => $provider->provider_id,
            ]);
        }
    }

    private function userServerProviders()
    {
        $csv = $this->createCSV('server-providers', [
            'provider',
            'label',
            'id'
        ]);

        foreach ($this->user->userServerProviders as $provider) {
            $csv->insertOne([
                'provider' => $provider->serverProvider->name,
                'label' => $provider->account,
                'id' => $provider->provider_id,
            ]);
        }
    }

    private function userRepositoryProviders()
    {
        $csv = $this->createCSV('user-repository-providers', [
            'provider',
            'label',
            'id'
        ]);

        foreach ($this->user->userRepositoryProviders as $provider) {
            $csv->insertOne([
                'provider' => $provider->repositoryProvider->name,
                'label' => $provider->repositoryProvider->name,
                'id' => $provider->provider_id,
            ]);
        }
    }

    private function userNotificationProviders()
    {
        $csv = $this->createCSV('user-notification-providers', [
            'provider',
            'label',
            'id'
        ]);

        foreach ($this->user->userNotificationProviders as $provider) {
            $csv->insertOne([
                'provider' => $provider->notificationProvider->name,
                'label' => $provider->notificationProvider->name,
                'id' => $provider->provider_id,
            ]);
        }
    }
    /*
    |--------------------------------------------------------------------------
    | SITES DATA
    |--------------------------------------------------------------------------
    */
    private function sites()
    {
        $csv = $this->createCSV('sites', [
            'name',
            'domain',
            'repository',
            'repository provider',
            'public_ssh_key',
            'private_ssh_key'
        ]);

        foreach ($this->user->sites as $site) {
            $csv->insertOne([
                'name' => $site->name,
                'domain' => $site->domain,
                'repository' => $site->repository,
                'repository provider' => $site->userRepositoryProvider->repositoryProvider->name,
                'public_ssh_key' => $site->public_ssh_key,
                'private_ssh_key' => $site->private_ssh_key
            ]);
        }

        $this->siteFiles();
        $this->siteSshKeys();
        $this->siteSchemas();
        $this->siteSchemaUsers();
        $this->siteEnvironmentVariables();
    }

    private function siteFiles()
    {
        $csv = $this->createCSV('site-files', [
            'site',
            'file',
            'contents'
        ]);

        foreach ($this->user->sites as $site) {
            foreach ($site->files as $file) {
                $csv->insertOne([
                    'site' => $site->name,
                    'file' => $file->file_path,
                    'contents' => $file->content
                ]);
            }
        }
    }

    private function siteSshKeys()
    {
        $csv = $this->createCSV('site-ssh-keys', [
            'site',
            'key name',
            'ssh key'
        ]);

        foreach ($this->user->sites as $site) {
            foreach ($site->sshKeys as $sshKey) {
                $csv->insertOne([
                    'site' => $site->name,
                    'key name' => $sshKey->name,
                    'ssh key' => $sshKey->ssh_key
                ]);
            }
        }
    }

    private function siteSchemas()
    {
        $csv = $this->createCSV('site-schemas', [
            'site',
            'schema',
            'database'
        ]);

        foreach ($this->user->sites as $site) {
            foreach ($site->schemas as $schema) {
                $csv->insertOne([
                    'site' => $site->name,
                    'schema' => $schema->name,
                    'database' => $schema->database,
                ]);
            }
        }
    }

    private function siteSchemaUsers()
    {
        $csv = $this->createCSV('site-schemas-users', [
            'site',
            'user',
            'password',
            'schema',
            'database'
        ]);

        foreach ($this->user->sites as $site) {
            foreach ($site->schemaUsers as $schemaUser) {
                foreach ($schemaUser->schema_ids as $schemaId) {
                    $schema = Schema::findOrFail($schemaId);
                    $csv->insertOne([
                        'site' => $site->name,
                        'user' => $schemaUser->name,
                        'password' => $schemaUser->password,
                        'schema' => $schema->name,
                        'database' => $schema->database,
                    ]);
                }
            }
        }
    }

    private function siteEnvironmentVariables()
    {
        $csv = $this->createCSV('site-environment-variables', [
            'site',
            'variable',
            'value'
        ]);

        foreach ($this->user->sites as $site) {
            foreach ($site->environmentVariables as $environmentVariable) {
                $csv->insertOne([
                    'site' => $site->name,
                    'variable' => $environmentVariable->variable,
                    'value' => $environmentVariable->value,
                ]);
            }
        }
    }

    /*
    |--------------------------------------------------------------------------
    | SERVERS DATA
    |--------------------------------------------------------------------------
    */
    private function servers()
    {
        $csv = $this->createCSV('servers', [
            'name',
            'ip',
            'public ssh key',
            'private ssh key',
            'sudo password',
            'database password',
        ]);

        foreach ($this->user->servers as $server) {
            $csv->insertOne([
                'name' => $server->name,
                'ip' => $server->ip,
                'public ssh key' => $server->public_ssh_key,
                'private ssh key' => $server->private_ssh_key,
                'sudo password' => $server->sudo_password,
                'database password' => $server->database_password,
            ]);
        }

        $this->serverFiles();
        $this->serverSshKeys();
        $this->serverSchemas();
        $this->serverSchemaUsers();
        $this->serverEnvironmentVariables();
    }

    private function serverFiles()
    {
        $csv = $this->createCSV('server-files', [
            'site',
            'file',
            'contents'
        ]);

        foreach ($this->user->servers as $server) {
            foreach ($server->files as $file) {
                $csv->insertOne([
                    'server' => $server->name,
                    'file' => $file->file_path,
                    'contents' => $file->content
                ]);
            }
        }
    }

    private function serverSshKeys()
    {
        $csv = $this->createCSV('server-ssh-keys', [
            'site',
            'key name',
            'ssh key'
        ]);

        foreach ($this->user->servers as $server) {
            foreach ($server->sshKeys as $sshKey) {
                $csv->insertOne([
                    'site' => $server->name,
                    'key name' => $sshKey->name,
                    'ssh key' => $sshKey->ssh_key
                ]);
            }
        }
    }

    private function serverSchemas()
    {
        $csv = $this->createCSV('server-schemas', [
            'site',
            'schema',
            'database'
        ]);

        foreach ($this->user->servers as $server) {
            foreach ($server->schemas as $schema) {
                $csv->insertOne([
                    'site' => $server->name,
                    'schema' => $schema->name,
                    'database' => $schema->database,
                ]);
            }
        }
    }

    private function serverSchemaUsers()
    {
        $csv = $this->createCSV('server-schemas-users', [
            'site',
            'user',
            'password',
            'schema',
            'database'
        ]);

        foreach ($this->user->servers as $server) {
            foreach ($server->schemaUsers as $schemaUser) {
                foreach ($schemaUser->schema_ids as $schemaId) {
                    $schema = Schema::findOrFail($schemaId);
                    $csv->insertOne([
                        'site' => $server->name,
                        'user' => $schemaUser->name,
                        'password' => $schemaUser->password,
                        'schema' => $schema->name,
                        'database' => $schema->database,
                    ]);
                }
            }
        }
    }

    private function serverEnvironmentVariables()
    {
        $csv = $this->createCSV('server-environment-variables', [
            'site',
            'variable',
            'value'
        ]);

        foreach ($this->user->servers as $server) {
            foreach ($server->environmentVariables as $environmentVariable) {
                $csv->insertOne([
                    'site' => $server->name,
                    'variable' => $environmentVariable->variable,
                    'value' => $environmentVariable->value,
                ]);
            }
        }
    }


    /**
     * @param $type
     * @param $headers
     * @return Writer
     * @throws \League\Csv\CannotInsertRecord
     */
    private function createCSV($type, $headers)
    {
        $csv = Writer::createFromPath("{$this->directory}/$type.csv", 'w');
        $csv->insertOne($headers);
        return $csv;
    }
}
