<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

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
            $table->integer('site_cron_job_id')->nullable();
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
        Schema::dropIfExists('server_cron_jobs');
    }
}
