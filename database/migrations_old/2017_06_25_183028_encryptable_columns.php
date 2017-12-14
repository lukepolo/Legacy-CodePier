<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class EncryptableColumns extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {




        Schema::table('user_notification_providers', function (Blueprint $table) {
            $table->renameColumn('tokenSecret', 'token_secret');
        });

        Schema::table('user_notification_providers', function (Blueprint $table) {
            $table->longText('token')->change();
            $table->longText('token_secret')->change();
            $table->longText('refresh_token')->change();
        });

        DB::table('user_server_providers')->truncate();
        DB::table('user_login_providers')->truncate();
        DB::table('user_repository_providers')->truncate();
        DB::table('user_notification_providers')->truncate();
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
