<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\KategoriController;
use App\Http\Controllers\Api\LokasiController;
use App\Http\Controllers\Api\SatuanController;
use App\Http\Controllers\Api\BarangController;
use App\Http\Controllers\Api\BarangMasukController;
use App\Http\Controllers\Api\BarangKeluarController;
use App\Http\Controllers\Api\DashboardController;
use App\Http\Controllers\Api\LaporanPdfController;
use App\Http\Controllers\Api\LaporanExcelController;
use App\Http\Controllers\Api\LaporanTransaksiController;
use App\Http\Controllers\Api\AiTransactionController;
use Illuminate\Support\Facades\Route;

// Auth routes
Route::prefix('auth')->group(function () {
    Route::post('/register', [AuthController::class, 'register']);
    Route::post('/login',    [AuthController::class, 'login']);
    Route::post('/guest-staff', [AuthController::class, 'guestStaffLogin']);

    Route::middleware('auth:sanctum')->group(function () {
        Route::post('/logout', [AuthController::class, 'logout']);
        Route::get('/me',      [AuthController::class, 'me']);
    });
});

// Protected Admin routes — wajib login
Route::middleware('auth:sanctum')->group(function () {
    // Inventory API routes
    Route::apiResource('kategoris', KategoriController::class);
    Route::apiResource('lokasis', LokasiController::class);
    Route::apiResource('satuans', SatuanController::class);
    Route::get('barangs/next-kode', [BarangController::class, 'nextKode']);
    Route::apiResource('barangs', BarangController::class);
    Route::apiResource('barang-masuks', BarangMasukController::class);
    Route::apiResource('barang-keluars', BarangKeluarController::class);
    Route::get('dashboard-stats', [DashboardController::class, 'index']);
    
    // AI Smart Input
    Route::post('ai/parse-chat', [AiTransactionController::class, 'parseChat']);
    Route::post('ai/confirm-transaction', [AiTransactionController::class, 'confirmTransaction']);

    // Laporan JSON
    Route::get('laporan/transaksi', [LaporanTransaksiController::class, 'index']);

    // Laporan PDF
    Route::get('laporan/stok/pdf',   [LaporanPdfController::class, 'stok']);
    Route::get('laporan/masuk/pdf',  [LaporanPdfController::class, 'masuk']);
    Route::get('laporan/keluar/pdf', [LaporanPdfController::class, 'keluar']);
    Route::get('laporan/transaksi/pdf', [LaporanPdfController::class, 'riwayatTransaksi']);

    // Laporan Excel
    Route::get('laporan/stok/excel',   [LaporanExcelController::class, 'stok']);
    Route::get('laporan/masuk/excel',  [LaporanExcelController::class, 'masuk']);
    Route::get('laporan/keluar/excel', [LaporanExcelController::class, 'keluar']);
    Route::get('laporan/transaksi/excel', [LaporanExcelController::class, 'riwayatTransaksi']);

    // User Management (Protected in Controller)
    Route::apiResource('users', \App\Http\Controllers\Api\UserController::class);
});
