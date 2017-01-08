<?php

use Illuminate\Database\Migrations\Migration;

class DeleteSitePublicKeys extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        \App\Models\Site\Site::whereNotNull('public_ssh_key')->update([
            'public_ssh_key' => null,
        ]);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
}
