<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Exports\TransactionExport;
use Maatwebsite\Excel\Facades\Excel;

class TransactionController extends Controller
{
    // 1. READ: Mengambil semua data transaksi milik user yang sedang login
    public function index() { 
        return response()->json(Auth::user()->transactions()->latest()->get()); 
    }

    // 2. CREATE: Menyimpan transaksi baru ke database
    public function store(Request $request) {
        $data = $request->validate([
            'title' => 'required|string|min:1',
            'amount' => 'required|numeric|min:1',
            'type' => 'required|in:income,expense',
            'category' => 'required|string', // <-- Tambahan Kategori
            'transaction_date' => 'required|date',
            'notes' => 'nullable|string',
        ]);
        
        $data['user_id'] = Auth::id(); 
        return response()->json(Transaction::create($data));
    }

    // 3. UPDATE: Mengedit transaksi yang sudah ada
    public function update(Request $request, $id) {
       $data = $request->validate([
            'title' => 'required|string|min:1',
            'amount' => 'required|numeric|min:1',
            'type' => 'required|in:income,expense',
            'category' => 'required|string', // <-- Tambahan Kategori
            'transaction_date' => 'required|date',
            'notes' => 'nullable|string',
        ]);

        $transaction = Auth::user()->transactions()->findOrFail($id);
        $transaction->update($data);
        return response()->json($transaction);
    }

    // 4. DELETE: Menghapus transaksi
    public function destroy($id) {
        $transaction = Auth::user()->transactions()->findOrFail($id);
        $transaction->delete();
        return response()->json(['message' => 'Deleted']);
    }

    // 5. EXPORT: Download data ke Excel
    public function export() {
        return Excel::download(new TransactionExport, 'Laporan_Keuangan.xlsx');
    }
}