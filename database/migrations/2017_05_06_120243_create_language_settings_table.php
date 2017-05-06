<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateLanguageSettingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('language_settings', function (Blueprint $table) {
            $table->increments('id');
            $table->string('class');
            $table->string('function');
            $table->json('data');
            $table->timestamps();
        });

        Schema::create('language_settingables', function (Blueprint $table) {
            $table->integer('language_setting_id');
            $table->integer('language_settingable_id');
            $table->string('language_settingable_type');
            $table->index(['language_setting_id', 'language_settingable_id', 'language_settingable_type'], 'language_settings');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('language_settings');
        Schema::dropIfExists('language_settingables');
    }
}
