<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSshKeysTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('ssh-keys', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name');
            $table->longText('ssh_key');
            $table->timestamps();
        });

        Schema::create('sshKeyables', function (Blueprint $table) {
            $table->unsignedInteger('ssh_key_id');
            $table->unsignedInteger('sshKeyable_id');
            $table->string('sshKeyable_type');
            $table->index(['ssh_key_id', 'sshKeyable_id', 'sshKeyable_type'], 'sshKeyable_indexs');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('ssh-keys');
        Schema::dropIfExists('sshKeyables');
    }
}
