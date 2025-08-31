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
<<<<<<< HEAD
    return response()->json([
        'message' => 'Laravel API is running',
        'status' => 'success',
        'timestamp' => now()
    ]);
=======
    return view('welcome');
>>>>>>> 0e32eed2a081eaba4f9a9655e9cacaab8f8c41b3
});