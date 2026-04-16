<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use Illuminate\Http\Request;
use App\Exports\TransactionExport;
use Maatwebsite\Excel\Facades\Excel;

class TransactionController extends Controller
{
    public function index() { 
        return response()->json(Transaction::latest()->get()); 
    }

    public function store(Request $request) {
        $data = $request->validate([
            'title' => 'required',
            'amount' => 'required|numeric',
            'type' => 'required|in:income,expense',
        ]);
        return response()->json(Transaction::create($data));
    }

    // FUNGSI EDIT ADA DI SINI
    public function update(Request $request, $id) {
        $data = $request->validate([
            'title' => 'required',
            'amount' => 'required|numeric',
            'type' => 'required|in:income,expense',
        ]);
        $transaction = Transaction::findOrFail($id);
        $transaction->update($data);
        return response()->json($transaction);
    }

    public function destroy($id) {
        Transaction::destroy($id);
        return response()->json(['message' => 'Deleted']);
    }

    public function export() {
        return Excel::download(new TransactionExport, 'Laporan_Keuangan_2026.xlsx');
    }
}