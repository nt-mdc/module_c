<?php

use App\Http\Controllers\HeritagePages;
use App\Http\Controllers\LisitingPageController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect()->route('home');
});

Route::prefix('xx_module_c')->group(function () {
    Route::get('/', [LisitingPageController::class, 'index'])->name('home');
    Route::get('/search', [LisitingPageController::class, 'indexSearchByKeyword'])->name('keyword.pages');
    Route::post('/search', [LisitingPageController::class, 'searchByKeyword']);
    Route::get('heritages/{path?}', [LisitingPageController::class, 'index'])->where('path', '.*')->name('list.pages');
    Route::get('tags/{tags?}', [LisitingPageController::class, 'searchByTags'])->where('tags', '.*')->name('search.pages');
});
