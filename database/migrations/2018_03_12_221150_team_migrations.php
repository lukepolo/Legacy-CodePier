<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class TeamMigrations extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::dropIfExists('pile_team');

        Schema::table('sites', function (Blueprint $table) {
            $table->unsignedInteger('team_id');
        });

        Schema::table('piles', function (Blueprint $table) {
            $table->unsignedInteger('team_id');
        });

        Schema::table('bitts', function (Blueprint $table) {
            $table->unsignedInteger('team_id');
        });

        Schema::table('teams', function ($table) {
            $table->string('stripe_id')->nullable();
            $table->string('card_brand')->nullable();
            $table->string('card_last_four')->nullable();
            $table->timestamp('trial_ends_at')->nullable();
        });

        Schema::table('subscriptions', function ($table) {
            $table->unsignedInteger('team_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
}
