<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateServersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('servers', function (Blueprint $table) {
            $table->string('name');
            $table->string('status');
            $table->increments('id');
            $table->integer('user_id');
            $table->integer('pile_id');
            $table->string('ip')->nullable();
            $table->json('options')->nullable();
            $table->integer('given_server_id')->nullable();
            $table->integer('server_provider_id');
            $table->integer('team_id')->nullable();
            $table->integer('progress')->default(0);
            $table->text('public_ssh_key')->nullable();
            $table->text('private_ssh_key')->nullable();
            $table->json('server_features')->nullable();
            $table->boolean('ssh_connection')->default(0);
            $table->longText('sudo_password')->nullable();
            $table->longText('database_password')->nullable();
            $table->json('server_provider_features')->nullable();
            $table->string('system_class');
            $table->json('stats')->nullable();
            $table->timestamps();
            $table->softDeletes();
            $table->longText('custom_server_url')->nullable();
            $table->string('custom_server_url')->change();
            $table->integer('port')->default(22);
            $table->string('type')->default('full_stack');
            $table->string('status')->nullable()->change()->default('');
            $table->json('slack_channel_preferences')->nullable();

            $table->index('user_id');
            $table->index('team_id');
            $table->index('pile_id');
            $table->index(['user_id', 'pile_id']);
            $table->index(['team_id', 'pile_id']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('servers');
    }
}
