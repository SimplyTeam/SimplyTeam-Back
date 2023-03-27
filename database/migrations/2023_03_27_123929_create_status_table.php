<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('status', function (Blueprint $table) {
            $table->string('id', 126)->primary();
            $table->string('label', 50);
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('status');
    }
};
