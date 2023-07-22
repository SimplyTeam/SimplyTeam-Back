<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('quests', function (Blueprint $table) {
            $table->unsignedBigInteger('previous_quest_id')->nullable();
            $table->unsignedBigInteger('level');
            $table->foreign('previous_quest_id')->references('id')->on('quests');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('quests', function (Blueprint $table) {
            $table->dropForeign(['previous_quest_id']);
            $table->dropColumn('previous_quest_id');
            $table->dropColumn('level');
        });
    }
};
