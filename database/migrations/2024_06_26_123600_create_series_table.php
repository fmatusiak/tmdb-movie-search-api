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
        Schema::create('series', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('external_id')->unique()->index();
            $table->json('title')->nullable();
            $table->json('overview')->nullable();
            $table->decimal('vote_average',5,3)->default(0)->index();
            $table->integer('vote_count')->default(0)->index();
            $table->decimal('popularity',8,3)->default(0)->index();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('series');
    }
};
