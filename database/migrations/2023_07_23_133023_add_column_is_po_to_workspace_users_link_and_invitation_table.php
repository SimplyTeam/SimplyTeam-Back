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
        Schema::table('link_between_users_and_workspaces', function (Blueprint $table) {
            $table->boolean('is_PO')->default(false);
        });

        Schema::table('workspaces_invitations', function (Blueprint $table) {
            $table->boolean('is_PO')->default(false);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('workspaces_invitations', function (Blueprint $table) {
            $table->dropColumn('is_PO');
        });

        Schema::table('link_between_users_and_workspaces', function (Blueprint $table) {
            $table->dropColumn('is_PO');
        });
    }
};
