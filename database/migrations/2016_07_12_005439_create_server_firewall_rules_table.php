<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

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
            $table->integer('site_firewall_rule_id')->nullable();
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
