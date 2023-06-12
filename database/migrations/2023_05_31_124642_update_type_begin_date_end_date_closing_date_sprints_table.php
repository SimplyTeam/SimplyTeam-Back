<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('sprints', function (Blueprint $table) {
            $table->date('begin_date')->change();
            $table->date('end_date')->change();
            $table->date('closing_date')->nullable();
        });
    }

    public function down()
    {
        Schema::table('sprints', function (Blueprint $table) {
            $table->datetime('begin_date')->change();
            $table->datetime('end_date')->change();
            $table->dropColumn('closing_date');
        });
    }
};
