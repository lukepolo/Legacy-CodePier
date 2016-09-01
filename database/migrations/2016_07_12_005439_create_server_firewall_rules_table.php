<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateServerFirewallRulesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('server_firewall_rules', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('server_id');
            $table->string('description');
            $table->integer('port');
            $table->string('from_ip')->nullable();
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
        Schema::drop('server_firewall_rules');
    }
}
