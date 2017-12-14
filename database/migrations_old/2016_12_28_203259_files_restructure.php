<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class FilesRestructure extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        \DB::statement('CREATE TABLE files LIKE site_files;');
        Schema::table('files', function (Blueprint $table) {
            $table->dropColumn('site_id');
            $table->integer('custom')->default(0);
        });

        Schema::create('fileables', function (Blueprint $table) {
            $table->integer('file_id');
            $table->integer('fileable_id');
            $table->string('fileable_type');
            $table->index(['file_id', 'fileable_id', 'fileable_type']);
        });

        $records = DB::table('site_files')->get();

        foreach ($records as $record) {
            $site = \App\Models\Site\Site::withTrashed()->find($record->site_id);

            if (! empty($site)) {
                unset($record->id);
                unset($record->site_id);

                $file = \App\Models\File::create((array) $record);

                $site->files()->save($file);
                foreach ($site->provisionedServers as $server) {
                    $server->files()->save($file);
                }
            }
        }

        Schema::dropIfExists('site_files');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('files');
        Schema::dropIfExists('fileables');
    }
}
