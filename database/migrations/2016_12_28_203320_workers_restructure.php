<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class WorkersRestructure extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        \DB::statement('CREATE TABLE workers LIKE site_workers;');
        Schema::table('workers', function(Blueprint $table) {
            $table->dropColumn('site_id');
        });

        Schema::create('workerables', function(Blueprint $table) {
            $table->integer('worker_id');
            $table->integer('workerables_id');
            $table->string('workerables_type');
            $table->index(['worker_id', 'workerables_id', 'workerables_type'], 'workerable_indexs');
        });

        $records = DB::table('server_workers')->get();

        foreach ($records as $record) {
            $server =\App\Models\Server\Server::withTrashed()->find($record->id);

            if(!empty($server)) {
                unset($record->site_worker_id);

                unset($record->id);
                unset($record->server_id);

                $newRecord = \App\Models\Worker::create((array) $record);

                $server->firewallRules()->save($newRecord);
            }
        }

        $records = DB::table('site_workers')->get();

        foreach ($records as $record) {
            $site =\App\Models\Site\Site::withTrashed()->find($record->id);

            if(!empty($site)) {

                unset($record->id);
                unset($record->site_id);

                $newRecord = \App\Models\Worker::create((array)$record);

                $site->firewallRules()->save($newRecord);
            }
        }

        Schema::dropIfExists('server_workers');
        Schema::dropIfExists('site_workers');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('workers');
        Schema::dropIfExists('workerables');
    }
}
