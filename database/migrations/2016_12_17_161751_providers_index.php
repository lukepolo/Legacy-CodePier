<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ProvidersIndex extends Migration
{
    const TABLES = [
//        'user_notification_providers' => [
//            'user_id',
//            ['notification_provider_id', 'provider_id']
//        ],
//        'user_repository_providers' => [
//            'user_id',
//            ['repository_provider_id', 'provider_id'],
//        ],
//        'user_server_providers' => [
//            'user_id',
//            ['server_provider_id', 'provider_id'],
//        ],
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
                    if (is_array($index)) {
                        $tableModifying->index($index, 'oauth_index');
                        continue;
                    }
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
        foreach (self::TABLES as $table =>  $indexes) {
            Schema::table($table, function (Blueprint $tableModifying) use ($indexes) {
                foreach ($indexes as $index) {
                    if (is_array($index)) {
                        $tableModifying->dropIndex('oauth_index');
                        continue;
                    }
                    $tableModifying->dropIndex($index);
                }
            });
        }
    }
}
