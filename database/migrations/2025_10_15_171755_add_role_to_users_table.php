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
        Schema::table('users', function (Blueprint $table) {
            // Add role column after email
            // enum: Only allows 'admin', 'manager', or 'user' values
            // default('user'): New users are regular users by default
            $table->enum('role', ['admin', 'manager', 'user'])
                ->default('user')
                ->after('email');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Remove the role column if migration is rolled back
            $table->dropColumn('role');
        });
    }
};
