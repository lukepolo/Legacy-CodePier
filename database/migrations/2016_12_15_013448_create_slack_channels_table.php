<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSlackChannelsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('slack_channels', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('slackable_id')->nullable();
            $table->string('slackable_type')->nullable();
            $table->string('channel');
            $table->boolean('created')->default(0);

            $table->timestamps();

            $table->index('slackable_id');
            $table->index('slackable_type');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('slack_channels');
    }
}
