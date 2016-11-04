<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateServerCronJobsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('server_cron_jobs', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('server_id');
            $table->integer('site_cron_job_id')->nullabe();
            $table->string('job');
            $table->string('user');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('server_cron_jobs');
    }
}
