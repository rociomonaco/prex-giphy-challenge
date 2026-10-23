<?php

use Illuminate\Support\Facades\Route;
use Src\IdentityAccess\Infrastructure\Http\LoginController;

Route::post('/v1/auth/login', LoginController::class)->name('auth.login');
