<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCommandServersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('commands', function (Blueprint $table) {
            $table->dropColumn('server_id');
            $table->dropColumn('started');
            $table->dropColumn('failed');
            $table->dropColumn('completed');
            $table->dropColumn('runtime');
            $table->dropColumn('log');
            $table->dropColumn('commandable_id');
            $table->dropColumn('commandable_type');
        });

        Schema::create('server_commands', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('server_id');
            $table->boolean('started')->default(0);
            $table->boolean('failed')->default(0);
            $table->boolean('completed')->default(0);
            $table->string('runtime')->nullable();
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
        Schema::dropIfExists('server_commands');

        Schema::table('commands', function (Blueprint $table) {
            $table->integer('server_id');
            $table->boolean('started')->default(0);
            $table->boolean('failed')->default(0);
            $table->boolean('completed')->default(0);
            $table->string('runtime')->nullable();
            $table->longText('log')->nullable();
            $table->integer('commandable_id')->nullable();
            $table->string('commandable_type')->nullable();
        });
    }
}
