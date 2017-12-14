<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSiteFirewallRulesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('firewall_rules', function (Blueprint $table) {
            $table->increments('id');
            $table->string('description');
            $table->string('port', 6)->nullable();
            $table->string('from_ip')->nullable();
            $table->string('type')->default('tcp');

            $table->timestamps();

            $table->index(['port', 'from_ip', 'type']);
        });

        Schema::create('firewallRuleables', function (Blueprint $table) {
            $table->integer('firewall_rule_id');
            $table->integer('firewallRuleable_id');
            $table->string('firewallRuleable_type');
            $table->index(['firewall_rule_id', 'firewallRuleable_id', 'firewallRuleable_type'], 'fireable_indexs');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('firewall_rules');
        Schema::dropIfExists('firewallRuleables');
    }
}
