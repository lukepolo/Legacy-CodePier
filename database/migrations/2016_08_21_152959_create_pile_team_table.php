<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePileTeamTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('pile_team', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('pile_id');
            $table->unsignedInteger('team_id');

            $table->index('pile_id');
            $table->index('team_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('pile_team');
    }
}
