<?php

use App\Http\Controllers\GenreController;
use Illuminate\Support\Facades\Route;

Route::prefix('genres')->group(function (){
    Route::get('/', [GenreController::class,'index']);
    Route::get('/{genreId}', [GenreController::class,'show']);
});
