<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateServerProvidersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('server_providers', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name');
            $table->string('provider_name');
            $table->string('provider_class');
            $table->boolean('oauth')->default(1);
            $table->boolean('secret_token')->default(0);

            $table->timestamps();

            $table->index('provider_name');
            $table->index('provider_class');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('server_providers');
    }
}
