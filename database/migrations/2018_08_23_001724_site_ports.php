<?php

use App\Models\User\User;
use App\Models\Site\Site;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class SitePorts extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('sites', function (Blueprint $table) {
            $table->smallInteger('port')->after('web_directory');
        });

        foreach (User::with('sites')->get() as $user) {
            foreach ($user->sites as $index => $site) {
                $site->update([
                    'port' => Site::STARTING_PORT + $index
                ]);
            }
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('sites', function (Blueprint $table) {
            $table->dropColumn('port');
        });
    }
}
