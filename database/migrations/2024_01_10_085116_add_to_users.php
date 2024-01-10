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
            $table->string('numeroIDCard_Passport')->nullable();
            $table->string('imgIDCard_Passport')->nullable();
            $table->string('Description_boutique')->nullable();
            $table->string('Emplacement_boutique')->nullable();
            $table->string('photo_coverage')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('numeroIDCard_Passport');
            $table->dropColumn('imgIDCard_Passport');
            $table->dropColumn('Description_boutique');
            $table->dropColumn('Emplacement_boutique');
            $table->dropColumn('photo_coverage');
        });
    }
};
