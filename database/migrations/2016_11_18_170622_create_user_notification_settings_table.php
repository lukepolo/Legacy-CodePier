<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUserNotificationSettingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('notification_settings', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name');
            $table->string('event');
            $table->json('services');
            $table->boolean('default');
            $table->string('description');
            $table->timestamps();
        });

        Schema::create('user_notification_settings', function (Blueprint $table) {
            $table->increments('id');
            $table->json('services');
            $table->integer('user_id');
            $table->integer('notification_setting_id');
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
        Schema::dropIfExists('notification_settings');
        Schema::dropIfExists('user_notification_settings');
    }
}
