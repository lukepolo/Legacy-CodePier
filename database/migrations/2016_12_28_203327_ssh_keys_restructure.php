<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class SshKeysRestructure extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        \DB::statement('CREATE TABLE ssh_keys LIKE site_ssh_keys;');
        Schema::table('ssh_keys', function(Blueprint $table) {
            $table->dropColumn('site_id');
        });

        Schema::create('sshKeyables', function(Blueprint $table) {
            $table->integer('ssh_key_id');
            $table->integer('sshKeyable_id');
            $table->string('sshKeyable_type');
            $table->index(['ssh_key_id', 'sshKeyable_id', 'sshKeyable_type'], 'sshKeyable_indexs');
        });

        $records = DB::table('server_ssh_keys')->get();

        foreach ($records as $record) {
            $server =\App\Models\Server\Server::withTrashed()->find($record->id);

            if(!empty($server)) {
                unset($record->site_worker_id);

                unset($record->id);
                unset($record->server_id);

                $newRecord = \App\Models\SshKey::create((array) $record);

                $server->sshKeys()->save($newRecord);
            }
        }

        $records = DB::table('site_ssh_keys')->get();

        foreach ($records as $record) {
            $site =\App\Models\Site\Site::withTrashed()->find($record->id);

            if(!empty($site)) {

                unset($record->id);
                unset($record->site_id);

                $newRecord = \App\Models\SshKey::create((array)$record);

                $site->sshKeys()->save($newRecord);
            }
        }

        Schema::dropIfExists('server_ssh_keys');
        Schema::dropIfExists('site_ssh_keys');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('ssh_keys');
        Schema::dropIfExists('sshKeyables');
    }
}
