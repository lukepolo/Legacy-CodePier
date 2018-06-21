<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSchemaUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('schema_users', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name');
            $table->json('schema_ids');
            $table->longText('password');

            $table->timestamps();
        });

        Schema::create('schema_userables', function (Blueprint $table) {
            $table->unsignedInteger('schema_user_id');
            $table->unsignedInteger('schema_userable_id');
            $table->string('schema_userable_type');

            $table->index(['schema_user_id', 'schema_userable_id', 'schema_userable_type'], 'schema_user_index');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('schema_users');
        Schema::dropIfExists('schema_userables');
    }
}
