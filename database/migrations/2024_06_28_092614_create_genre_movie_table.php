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
        Schema::create('genre_movie', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('genre_id')->index();
            $table->unsignedBigInteger('movie_id')->index();

            $table->foreign('genre_id')->references('id')->on('genres')->cascadeOnDelete();
            $table->foreign('movie_id')->references('id')->on('movies')->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('genre_movie');
    }
};
