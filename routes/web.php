<?php

use App\Http\Controllers\TransactionController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () { return view('welcome'); });

// Jalur API buat Frontend lu
Route::get('/api/transactions', [TransactionController::class, 'index']);
Route::post('/api/transactions', [TransactionController::class, 'store']);
Route::put('/api/transactions/{id}', [TransactionController::class, 'update']); // Jalur Edit
Route::delete('/api/transactions/{id}', [TransactionController::class, 'destroy']);
Route::get('/api/export', [TransactionController::class, 'export']);