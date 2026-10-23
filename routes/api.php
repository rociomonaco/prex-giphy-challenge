<?php

use Illuminate\Support\Facades\Route;
use Src\Audit\Infrastructure\Http\AuditInteraction;
use Src\Favorites\Infrastructure\Http\SaveFavoriteGifController;
use Src\Gifs\Infrastructure\Http\FindGifController;
use Src\Gifs\Infrastructure\Http\SearchGifsController;
use Src\IdentityAccess\Infrastructure\Http\LoginController;

Route::middleware(AuditInteraction::class)->group(function (): void {
    Route::post('/v1/auth/login', LoginController::class)->name('auth.login');

    Route::prefix('v1')->middleware('auth:api')->group(function (): void {
        Route::get('/gifs/search', SearchGifsController::class)->name('gifs.search');
        Route::get('/gifs/{id}', FindGifController::class)->where('id', '[A-Za-z0-9]+')->name('gifs.show');
        Route::post('/favorites', SaveFavoriteGifController::class)->name('favorites.store');
    });
});
