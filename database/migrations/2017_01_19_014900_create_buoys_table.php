<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateBuoysTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('buoys', function (Blueprint $table) {
            $table->increments('id');
            $table->string('domain')->nullable();
            $table->string('buoy_class');
            $table->json('options')->nullable();
            $table->timestamps();
        });

        Schema::create('bouyable', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('bouyable_id');
            $table->string('bouyable_type');
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
        Schema::dropIfExists('buoys');
        Schema::dropIfExists('bouyable');
    }
}
