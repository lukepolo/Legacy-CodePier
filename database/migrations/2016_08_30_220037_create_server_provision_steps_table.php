<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

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
            $table->integer('server_id');
            $table->string('service');
            $table->string('function');
            $table->string('step');
            $table->text('parameters');
            $table->boolean('completed')->default(0);
            $table->boolean('failed')->default(0);
            $table->longText('log')->nullable();

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
        Schema::drop('server_provision_steps');
    }
}
