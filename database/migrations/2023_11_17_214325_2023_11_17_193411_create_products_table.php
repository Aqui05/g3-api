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
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->text('description');
            $table->integer('quantity')->nullable();
            $table->double('prix',10,2)->nullable();
            $table->unsignedBigInteger('user_id');
            $table->string('photo_path', 2048)->nullable();
            $table->timestamps();
            $table->unsignedBigInteger('categorie_id');

            //Contraintes
            $table->foreign('user_id')->references('id')->on('users');
            $table->foreign('categorie_id')->references('id')->on('categories');

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
