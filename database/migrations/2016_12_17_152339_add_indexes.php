<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddIndexes extends Migration
{
    CONST TABLES = [
//        'auth_codes' => [
//            'user_id'
//        ],
//        'bitts' => [
//            'user_id'
//        ],
//        'commands' => [
//            'site_id',
//            'server_id',
//            ['commandable_id', 'commandable_type']
//        ],
//        'deployment_events' => [
//            'deployment_step_id',
//            'site_server_deployment_id'
//        ],
//        'deployment_steps' => [
//            'site_id',
//        ],
//        'notification_providers' => [
//            'provider_name'
//        ],
//        'pile_team' => [
//            'pile_id',
//            'team_id',
//        ],
//        'piles' => [
//            'user_id'
//        ],
//        'repository_providers' => [
//            'provider_name'
//        ],
//        'server_commands' => [
//            'command_id',
//            'server_id',
//            ['failed', 'completed']
//        ],
//        'server_cron_jobs' => [
//            'server_id',
//            'site_cron_job_id',
//            ['job', 'user'],
//        ],
//        'server_firewall_rules' => [
//            'server_id',
//            'site_firewall_rule_id',
//            ['port', 'from_ip']
//        ],
//        'server_network_rules' => [
//            'server_id',
//        ],
//        'server_provider_features' => [
//            'server_provider_id',
//        ],
//        'server_provider_options' => [
//            'server_provider_id',
//        ],
//        'server_provider_regions' => [
//            'server_provider_id',
//        ],
//        'server_providers' => [
//            'provider_name'
//        ],
//        'server_provision_steps' => [
//            'server_id'
//        ],
//        'server_site' => [
//            'server_id',
//            'site_id'
//        ],
//        'server_ssh_keys' => [
//            'site_ssh_key_id',
//            'server_id',
//        ],
//        'server_ssl_certificates' => [
//            'site_ssl_certificate_id',
//            ['type', 'domains']
//        ],
//        'servers' => [
//            'user_id',
//            ['user_id', 'pile_id'],
//            'pile_id',
//            'team_id',
//            ['team_id', 'pile_id']
//        ],
//        'site_cron_jobs' => [
//            'site_id',
//            ['job', 'user'],
//        ],
//        'site_deployments' => [
//            'site_id',
//        ],
//        'site_files' => [
//            'site_id',
//        ],
//        'site_firewall_rules' => [
//            'site_id',
//            ['port', 'from_ip']
//        ],
//        'site_server_deployments' => [
//            'server_id',
//            'site_deployment_id'
//        ],
        'site_ssh_keys' => [
            'site_id',
        ],
        'site_ssl_certificates' => [
            'site_id',
            ['type', 'domains']
        ],
        'site_workers' => [
            'site_id'
        ],
        'sites' => [
            'user_id',
            'pile_id',
            ['user_id', 'pile_id']
        ],
        'slack_channels' => [
            'slackable_id',
            'slackable_type'
        ],
        'subscriptions' => [
            'user_id'
        ],
        'taggables' => [
            'taggable_id',
            'taggable_type'
        ],
        'teams' => [
            'owner_id'
        ],
        'user_login_providers' => [
            ['provider', 'provider_id']
        ],
        'user_notification_settings' => [
            'user_id'
        ],
        'user_ssh_keys' => [
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
        foreach(self::TABLES as $table =>  $indexes) {
            Schema::table($table, function (Blueprint $tableModifying)  use($indexes) {
                foreach($indexes as $index) {
                    $tableModifying->index($index);
                }
            });
        }


        Schema::table('users', function (Blueprint $tableModifying) {
            $tableModifying->dropIndex('users_email_unique');
            $tableModifying->index('user_login_provider_id');
            $tableModifying->unique(['email', 'user_login_provider_id']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        foreach(self::TABLES as $table =>  $indexes) {
            Schema::table($table, function (Blueprint $tableModifying)  use($table, $indexes) {
                foreach($indexes as $index) {
                    if(is_array($index)) {
                        $tableModifying->dropIndex($index);
                        continue;
                    }
                    $tableModifying->dropIndex($table.'_'.$index.'_index');
                }
            });
        }

        Schema::table('users', function (Blueprint $tableModifying) {
            $tableModifying->unique('email');
            $tableModifying->dropIndex('users_user_login_provider_id_index');
            $tableModifying->dropUnique(['email', 'user_login_provider_id']);
        });
    }
}
