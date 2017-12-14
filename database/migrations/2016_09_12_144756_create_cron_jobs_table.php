<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCronJobsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('cron_jobs', function (Blueprint $table) {
            $table->increments('id');
            $table->string('job');
            $table->string('user');
            $table->json('server_ids')->nullable();
            $table->json('server_types')->nullable();

            $table->timestamps();

            $table->index(['job', 'user']);
        });

        Schema::create('cronJobables', function (Blueprint $table) {
            $table->integer('cron_job_id');
            $table->integer('cronJobable_id');
            $table->string('cronJobable_type');
            $table->index(['cron_job_id', 'cronJobable_id', 'cronJobable_type']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('cron_jobs');
        Schema::dropIfExists('cronJobables');
    }
}
