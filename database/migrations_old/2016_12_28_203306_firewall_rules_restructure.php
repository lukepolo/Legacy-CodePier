<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class FirewallRulesRestructure extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        \DB::statement('CREATE TABLE firewall_rules LIKE site_firewall_rules;');
        Schema::table('firewall_rules', function (Blueprint $table) {
            $table->dropColumn('site_id');
        });

        Schema::create('firewallRuleables', function (Blueprint $table) {
            $table->integer('firewall_rule_id');
            $table->integer('firewallRuleable_id');
            $table->string('firewallRuleable_type');
            $table->index(['firewall_rule_id', 'firewallRuleable_id', 'firewallRuleable_type'], 'fireable_indexs');
        });

        $records = DB::table('server_firewall_rules')->get();

        foreach ($records as $record) {
            $server = \App\Models\Server\Server::withTrashed()->find($record->server_id);

            if (! empty($server)) {
                unset($record->site_firewall_rule_id);

                unset($record->id);
                unset($record->server_id);

                $newRecord = \App\Models\FirewallRule::create((array) $record);

                $server->firewallRules()->save($newRecord);
            }
        }

        $records = DB::table('site_firewall_rules')->get();

        foreach ($records as $record) {
            $site = \App\Models\Site\Site::withTrashed()->find($record->site_id);

            if (! empty($site)) {
                unset($record->id);
                unset($record->site_id);

                $newRecord = \App\Models\FirewallRule::create((array) $record);

                $site->firewallRules()->save($newRecord);
            }
        }

        Schema::dropIfExists('server_firewall_rules');
        Schema::dropIfExists('site_firewall_rules');
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
