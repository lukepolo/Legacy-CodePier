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
            $table->increments('id');

            $table->string('name');
            $table->string('type')->default('full_stack');
            $table->string('ip')->nullable();
            $table->string('status')->nullable()->default('');
            $table->string('system_class');


            $table->unsignedInteger('user_id');
            $table->unsignedInteger('pile_id');
            $table->unsignedInteger('team_id')->nullable();
            $table->unsignedInteger('server_provider_id');

            $table->unsignedInteger('given_server_id')->nullable();
            $table->unsignedInteger('progress')->default(0);

            $table->text('public_ssh_key')->nullable();
            $table->text('private_ssh_key')->nullable();

            $table->longText('sudo_password')->nullable();
            $table->longText('database_password')->nullable();

            $table->longText('custom_server_url')->nullable();
            $table->unsignedInteger('port')->default(22);
            $table->boolean('ssh_connection')->default(0);

            $table->json('stats')->nullable();
            $table->json('options')->nullable();
            $table->json('server_features')->nullable();
            $table->json('server_provider_features')->nullable();
            $table->json('slack_channel_preferences')->nullable();

            $table->timestamps();
            $table->softDeletes();


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
