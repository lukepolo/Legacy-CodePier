<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUserNotificationProvidersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('user_notification_providers', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('user_id');
            $table->integer('notification_provider_id');
            $table->string('provider_id');
            $table->string('token');
            $table->string('refresh_token')->nullable();
            $table->string('expires_in')->nullable();
            $table->string('tokenSecret')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('user_notification_providers');
    }
}
