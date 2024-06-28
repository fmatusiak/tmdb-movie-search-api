<?php

use App\Http\Controllers\GenreController;
use App\Http\Controllers\MovieController;
use Illuminate\Support\Facades\Route;

Route::prefix('genres')->group(function () {
    Route::get('/', [GenreController::class, 'index']);
    Route::get('/{genreId}', [GenreController::class, 'show']);
});

Route::prefix('movies')->group(function () {
    Route::get('/', [MovieController::class, 'index']);
    Route::get('/{movieId}', [MovieController::class, 'show']);
});
