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
        Schema::table('workspaces_invitations', function (Blueprint $table) {
            $table->foreignId('invited_by_id')
                ->nullable()
                ->constrained('users')
                ->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('workspaces_invitations', function (Blueprint $table) {
            $table->dropForeign(['invited_by_id']);
            $table->dropColumn('invited_by_id');
        });
    }
};
