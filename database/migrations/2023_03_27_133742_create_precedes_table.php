<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('precedes', function (Blueprint $table) {
            $table->unsignedBigInteger('previous_task_id');
            $table->unsignedBigInteger('next_task_id');
            $table->primary(['previous_task_id', 'next_task_id']);
            $table->foreign('previous_task_id')->references('id')->on('tasks')->onDelete('cascade');
            $table->foreign('next_task_id')->references('id')->on('tasks')->onDelete('cascade');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('precedes');
    }
};
