<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddIndexes extends Migration
{
    const TABLES = [
        'auth_codes' => [
            'user_id',
        ],
        'bitts' => [
            'user_id',
        ],
        'commands' => [
            'site_id',
            'server_id',
            ['commandable_id', 'commandable_type'],
        ],
        'deployment_events' => [
            'deployment_step_id',
            'site_server_deployment_id',
        ],
        'deployment_steps' => [
            'site_id',
        ],
        'notification_providers' => [
            'provider_name',
        ],
        'pile_team' => [
            'pile_id',
            'team_id',
        ],
        'piles' => [
            'user_id',
        ],
        'server_commands' => [
            'command_id',
            'server_id',
            ['failed', 'completed'],
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
        'server_provision_steps' => [
            'server_id',
        ],
        'server_site' => [
            'server_id',
            'site_id',
        ],
        'server_ssl_certificates' => [
            'site_ssl_certificate_id',
            ['type', 'domains'],
        ],
        'site_cron_jobs' => [
            'site_id',
            ['job', 'user'],
        ],
        'site_deployments' => [

        ],
        'site_files' => [
            'site_id',
        ],
        'site_firewall_rules' => [
            'site_id',
            ['port', 'from_ip'],
        ],
        'site_server_deployments' => [
            'server_id',
            'site_deployment_id',
        ],
        'site_ssh_keys' => [
            'site_id',
        ],
        'slack_channels' => [
            'slackable_id',
            'slackable_type',
        ],
        'subscriptions' => [
            'user_id',
        ],
        'taggables' => [
            'taggable_id',
            'taggable_type',
        ],
        'teams' => [
            'owner_id',
        ],
        'user_notification_settings' => [
            'user_id',
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
