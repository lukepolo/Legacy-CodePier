<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateProvisioningKeysTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('provisioning_keys', function (Blueprint $table) {
            $table->string('key', 40);
            $table->unsignedInteger('user_id');
            $table->unsignedInteger('server_id');
            $table->timestamps();

            $table->index(['user_id', 'server_id']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('provisioning_keys');
    }
}
