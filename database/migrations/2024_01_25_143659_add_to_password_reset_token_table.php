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
<<<<<<< HEAD
        Schema::table('password_reset_tokens', function (Blueprint $table) {
=======
        Schema::table('password_reset_token', function (Blueprint $table) {
>>>>>>> e5d35cc (Y commit)
            //
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
<<<<<<< HEAD
        Schema::table('password_reset_tokens', function (Blueprint $table) {
=======
        Schema::table('password_reset_token', function (Blueprint $table) {
>>>>>>> e5d35cc (Y commit)
            //
        });
    }
};
