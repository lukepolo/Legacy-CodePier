<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class DeleteIsPrivateAndImported extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table("sites", function (Blueprint $table) {
            $table->dropColumn('private');
            $table->dropColumn('ssh_key_imported');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table("sites", function (Blueprint $table) {
            $table->boolean('private');
            $table->boolean('ssh_key_imported');
        });
    }
}
