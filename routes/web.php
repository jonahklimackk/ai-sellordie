<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\FighterCardController;

Route::get('/', function () {
    return view('welcome');
});



Route::get('/fighter-card-generator', [FighterCardController::class, 'index']);
Route::post('/fighter-card-generator', [FighterCardController::class, 'generate'])->name('fighter-card.generate');