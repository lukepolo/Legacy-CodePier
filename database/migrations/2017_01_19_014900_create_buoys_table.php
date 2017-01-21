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
        Schema::create('buoy_apps', function (Blueprint $table) {
            $table->increments('id');
            $table->string('title');
            $table->string('icon');
            $table->string('buoy_class');
            $table->longText('description');
            $table->json('options')->nullable();
            $table->boolean('active')->default(1);
            $table->integer('local_port');
            $table->integer('docker_port');
            $table->timestamps();

            $table->index('title');
        });

        Schema::create('buoys', function (Blueprint $table) {
            $table->increments('id');
            $table->string('domain')->nullable();
            $table->integer('buoy_app_id');
            $table->json('options')->nullable();
            $table->string('status')->nullable();
            $table->integer('local_port');
            $table->timestamps();

            $table->index('buoy_app_id');
        });

        Schema::create('bouyable', function (Blueprint $table) {
            $table->integer('buoy_id');
            $table->integer('bouyable_id');
            $table->string('bouyable_type');
            $table->timestamps();

            $table->index(['buoy_id', 'bouyable_id', 'bouyable_type']);
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
        Schema::dropIfExists('buoy_apps');
    }
}
