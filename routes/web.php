<?php

use Illuminate\Support\Facades\Route;

// Beri nama route 'login' agar Laravel tidak error saat redirect unauthorized user
Route::get('/login', function () {
    return view('app');
})->name('login');

// Route untuk mematikan server PHP (Tombol Close Server)
Route::post('/shutdown', function () {
    exec('taskkill /F /IM sistok-server.exe');
    return response()->json(['message' => 'Server shutting down']);
});

// Semua request ke blade, Vue yang handle routing
Route::get('/{any?}', function () {
    return view('app');
})->where('any', '.*');
