<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateServerProvisionStepsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('server_provision_steps', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('server_id');
            $table->string('service');
            $table->string('function');
            $table->string('step');
            $table->json('parameters');
            $table->boolean('completed')->default(0);
            $table->boolean('failed')->default(0);
            $table->json('log')->nullable();

            $table->timestamps();

            $table->index('server_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('server_provision_steps');
    }
}
