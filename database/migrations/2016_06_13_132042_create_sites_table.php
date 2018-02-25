<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSitesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sites', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name');
            $table->string('domain');
            $table->string('branch')->nullable();
            $table->text('repository')->nullable();

            $table->unsignedInteger('user_id');
            $table->unsignedInteger('pile_id');
            $table->unsignedInteger('user_repository_provider_id')->nullable();

            $table->string('type')->nullable();
            $table->string('framework')->nullable();
            $table->string('web_directory')->nullable();
            $table->boolean('wildcard_domain')->default(0);
            $table->boolean('zero_downtime_deployment')->default(0);
            $table->unsignedInteger('automatic_deployment_id')->nullable();
            $table->unsignedInteger('keep_releases')->default(10);

            $table->boolean('private')->default(0);
            $table->boolean('ssh_key_imported')->default(0);

            $table->text('public_ssh_key')->nullable();
            $table->text('private_ssh_key')->nullable();

            $table->json('workflow')->nullable();
            $table->json('server_features')->nullable();
            $table->json('slack_channel_preferences')->nullable();

            $table->string('hash', 40)->nullable()->unique();

            $table->timestamps();
            $table->softDeletes();

            $table->index('user_id');
            $table->index('pile_id');
            $table->index(['user_id', 'pile_id']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('sites');
    }
}
