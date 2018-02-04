<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateBackupsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('backups', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name');
            $table->json('items');
            $table->string('type');
            $table->timestamps();
        });

        Schema::create('backupables', function (Blueprint $table) {
            $table->unsignedInteger('backup_id');
            $table->unsignedInteger('backupable_id');
            $table->string('backupable_type');
            $table->index(['backup_id', 'backupable_id', 'backupable_type'], 'backupable_indexs');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('backups');
        Schema::dropIfExists('backupables');
    }
}
