<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class Add2faToUser extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('second_auth_secret')->nullable();
            $table->boolean('second_auth_active')->default(false);
            $table->integer('second_auth_updated_at')->nullable();
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
            $table->dropColumn('second_auth_secret');
            $table->dropColumn('second_auth_active');
            $table->dropColumn('second_auth_updated_at');
        });
    }
}
