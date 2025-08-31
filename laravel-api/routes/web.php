<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    return response()->json([
        'message' => 'Laravel API is running',
        'status' => 'success',
        'timestamp' => now(),
        'version' => '1.0.0'
    ]);
});

// Health check for web
Route::get('/health', function () {
    return response()->json([
        'status' => 'ok',
        'service' => 'Laravel Web',
        'timestamp' => now()
    ]);
});