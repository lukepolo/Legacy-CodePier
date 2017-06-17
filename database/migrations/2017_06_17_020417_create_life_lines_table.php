<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateLifelinesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('lifelines', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name');
            $table->integer('threshold');
            $table->integer('site_id');
            $table->dateTime('last_seen')->nullable();
            $table->integer('sent_notifications')->default(0);
            $table->timestamps();
            $table->index(['last_seen', 'threshold', 'sent_notifications']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('lifelines');
    }
}
