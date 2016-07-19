<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddFromIpToFirewall extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('server_firewall_rules', function (Blueprint $table) {
            $table->string('from_ip')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('server_firewall_rules', function (Blueprint $table) {
            $table->dropColumn('from_ip');
        });
    }
}
