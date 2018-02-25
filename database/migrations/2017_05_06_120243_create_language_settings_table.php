<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

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
            $table->string('setting');
            $table->string('language');
            $table->json('params')->nullable();
            $table->timestamps();
        });

        Schema::create('language_settingables', function (Blueprint $table) {
            $table->unsignedInteger('language_setting_id');
            $table->unsignedInteger('language_settingable_id');
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
