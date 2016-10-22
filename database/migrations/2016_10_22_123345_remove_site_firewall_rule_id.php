<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class RemoveSiteFirewallRuleId extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('server_firewall_rules', function (Blueprint $table) {
            $table->dropColumn('site_firewall_rule_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('server_firewall_rules', function (Blueprint $table) {
            $table->integer('site_firewall_rule_id');
        });
    }
}
