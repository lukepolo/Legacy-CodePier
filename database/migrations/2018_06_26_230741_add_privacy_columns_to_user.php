<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddPrivacyColumnsToUser extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->boolean('processing')->default(true)->before('updated_at');
            $table->boolean('marketing')->default(true)->before('updated_at');
            $table->dateTime('last_bundle_download')->nullable()->before('updated_at');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('processing');
            $table->dropColumn('marketing');
            $table->dropColumn('last_bundle_download');
        });
    }
}
