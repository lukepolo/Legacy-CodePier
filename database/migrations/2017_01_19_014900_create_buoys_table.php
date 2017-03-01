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
            $table->string('buoy_class');
            $table->longText('description')->nullable();
            $table->string('icon')->nullable();
            $table->text('options')->nullable();
            $table->text('ports')->nullable();
            $table->boolean('active')->default(1);
            $table->timestamps();

            $table->index('title');
        });

        Schema::create('buoys', function (Blueprint $table) {
            $table->increments('id');
            $table->string('domain')->nullable();
            $table->integer('buoy_app_id');
            $table->text('options')->nullable();
            $table->text('ports')->nullable();
            $table->string('status')->nullable();
            $table->timestamps();

            $table->index('buoy_app_id');
        });

        Schema::create('buoyables', function (Blueprint $table) {
            $table->integer('buoy_id');
            $table->integer('buoyable_id');
            $table->string('buoyable_type');
            $table->timestamps();

            $table->index(['buoy_id', 'buoyable_id', 'buoyable_type']);
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
        Schema::dropIfExists('buoyables');
        Schema::dropIfExists('buoy_apps');
    }
}
