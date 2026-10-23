<?php

use Illuminate\Support\Facades\Route;

Route::get('/', fn () => response()->json([
    'name' => 'Prex GIPHY API',
    'status' => 'ok',
]));
