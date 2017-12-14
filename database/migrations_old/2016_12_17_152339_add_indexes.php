<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddIndexes extends Migration
{
    const TABLES = [
        'bitts' => [
            'user_id',
        ],
        'commands' => [

        ],

        'server_cron_jobs' => [
            'server_id',
            'site_cron_job_id',
            ['job', 'user'],
        ],
        'server_firewall_rules' => [
            'server_id',
            'site_firewall_rule_id',
            ['port', 'from_ip'],
        ],
        'server_network_rules' => [
            'server_id',
        ],
        'site_cron_jobs' => [
            'site_id',
            ['job', 'user'],
        ],
        'site_deployments' => [

        ],
        'site_firewall_rules' => [

        ],
        'subscriptions' => [
            'user_id',
        ],
        'teams' => [
            'owner_id',
        ],
    ];

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        foreach (self::TABLES as $table =>  $indexes) {
            Schema::table($table, function (Blueprint $tableModifying) use ($indexes) {
                foreach ($indexes as $index) {
                    $tableModifying->index($index);
                }
            });
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('users', function (Blueprint $tableModifying) {
            $tableModifying->unique('email');
            $tableModifying->dropIndex('users_user_login_provider_id_index');
            $tableModifying->dropUnique(['email', 'user_login_provider_id']);
        });
    }
}
