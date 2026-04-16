<?php

use App\Http\Controllers\TransactionController;
use Illuminate\Support\Facades\Route;

// Halaman utama sekarang pake middleware 'auth'
// Tambahkan ->name('dashboard') di bagian akhir
Route::get('/', function () {
    return view('welcome');
})->middleware(['auth', 'verified'])->name('dashboard');

// API Routes
Route::get('/api/transactions', [TransactionController::class, 'index']);
Route::post('/api/transactions', [TransactionController::class, 'store']);
Route::put('/api/transactions/{id}', [TransactionController::class, 'update']);
Route::delete('/api/transactions/{id}', [TransactionController::class, 'destroy']);
Route::get('/api/export', [TransactionController::class, 'export']);


require __DIR__.'/auth.php'; // Ini rute bawaan Laravel Breeze