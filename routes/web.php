<?php

use App\Http\Controllers\HeritagePages;
use App\Http\Controllers\LisitingPageController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::prefix('xx_module_c')->group(function () {
    Route::get('heri', [HeritagePages::class, 'index']);
    Route::get('/', [LisitingPageController::class, 'index'])->name('home');
    Route::get('heritages/{path?}', [LisitingPageController::class, 'index'])->where('path', '.*')->name('list.pages');
});
