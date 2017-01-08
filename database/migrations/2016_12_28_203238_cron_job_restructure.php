<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CronJobRestructure extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        \DB::statement('CREATE TABLE cron_jobs LIKE site_cron_jobs;');
        Schema::table('cron_jobs', function (Blueprint $table) {
            $table->dropColumn('site_id');
        });

        Schema::create('cronjobables', function (Blueprint $table) {
            $table->integer('cron_job_id');
            $table->integer('cronjobable_id');
            $table->string('cronjobable_type');
            $table->index(['cron_job_id', 'cronjobable_id', 'cronjobable_type']);
        });

        $records = DB::table('server_cron_jobs')->get();

        foreach ($records as $record) {
            $server = \App\Models\Server\Server::withTrashed()->find($record->server_id);

            if (! empty($server)) {
                unset($record->site_cron_job_id);

                unset($record->id);
                unset($record->server_id);

                $cronJob = \App\Models\CronJob::create((array) $record);

                $server->cronJobs()->save($cronJob);
            }
        }

        $records = DB::table('site_cron_jobs')->get();

        foreach ($records as $record) {
            $site = \App\Models\Site\Site::withTrashed()->find($record->site_id);

            if (! empty($site)) {
                unset($record->id);
                unset($record->site_id);

                $cronJob = \App\Models\CronJob::create((array) $record);

                $site->cronJobs()->save($cronJob);
            }
        }

        Schema::dropIfExists('server_cron_jobs');
        Schema::dropIfExists('site_cron_jobs');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('cron_jobs');
        Schema::dropIfExists('cronjobables');
    }
}
