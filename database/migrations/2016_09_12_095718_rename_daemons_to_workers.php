<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Migrations\Migration;

class RenameDaemonsToWorkers extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::rename('server_daemons', 'server_workers');
        Schema::rename('site_daemons', 'site_workers');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::rename('server_workers', 'server_daemons');
        Schema::rename('site_daemons', 'site_daemons');
    }
}
