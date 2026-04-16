<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use Illuminate\Http\Request;
use App\Exports\TransactionExport;
use Maatwebsite\Excel\Facades\Excel;
class TransactionController extends Controller
{
    // Ambil semua data via API
    public function index() {
        return response()->json(Transaction::latest()->get());
    }

    // Simpan data baru
    public function store(Request $request) {
        $data = $request->validate([
            'title' => 'required',
            'amount' => 'required|numeric',
            'type' => 'required|in:income,expense',
        ]);

        $transaction = Transaction::create($data);
        return response()->json($transaction);
    }

    // Hapus data
    public function destroy($id) {
        Transaction::destroy($id);
        return response()->json(['message' => 'Deleted']);
    }
    public function export() 
    {
    // Mengunduh file dengan nama ini 
    return Excel::download(new TransactionExport, 'Laporan_Keuangan_2026.xlsx');
    }
}