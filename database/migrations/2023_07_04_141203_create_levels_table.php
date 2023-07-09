<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('levels', function (Blueprint $table) {
            $table->unsignedInteger('id')->primary();
            $table->unsignedInteger('max_point');
            $table->unsignedInteger('min_point');
        });
    }

    public function down()
    {
        Schema::dropIfExists('levels');
    }
};
