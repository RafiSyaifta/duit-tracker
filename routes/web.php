<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\TransactionController;

Route::get('/', function () { return view('welcome'); });
Route::get('/api/transactions', [TransactionController::class, 'index']);
Route::post('/api/transactions', [TransactionController::class, 'store']);
Route::delete('/api/transactions/{id}', [TransactionController::class, 'destroy']);
Route::get('/api/export', [TransactionController::class, 'export']);