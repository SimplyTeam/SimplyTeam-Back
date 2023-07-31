<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::table('users_quests', function (Blueprint $table) {
            $table->dateTime('date_completed')->nullable()->change();
        });
    }


    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::table('users_quests', function (Blueprint $table) {
            $table->dateTime('date_completed')->nullable(false)->change();
        });
    }
};
