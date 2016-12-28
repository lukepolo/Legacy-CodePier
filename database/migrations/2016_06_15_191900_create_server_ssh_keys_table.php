<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateServerSshKeysTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('server_ssh_keys', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('site_ssh_key_id')->nullable();
            $table->integer('server_id');
            $table->string('name');
            $table->longText('ssh_key');
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
        Schema::dropIfExists('server_ssh_keys');
    }
}
