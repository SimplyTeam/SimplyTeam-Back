<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('users_quests', function (Blueprint $table) {
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('quest_id');
            $table->integer('completed_count');
            $table->boolean('is_completed');
            $table->dateTime('date_completed');
            $table->primary(['user_id', 'quest_id']);
            $table->foreign('user_id')->references('id')->on('users');
            $table->foreign('quest_id')->references('id')->on('quests');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users_quests');
    }
};
