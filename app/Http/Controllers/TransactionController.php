<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use Illuminate\Http\Request;
use App\Exports\TransactionExport;
use Maatwebsite\Excel\Facades\Excel;
// TAMBAHKAN BARIS INI BIAR VS CODE PAHAM
use Illuminate\Support\Facades\Auth;

class TransactionController extends Controller
{
    public function index() { 
        // Ganti auth()->user() jadi Auth::user()
        return response()->json(Auth::user()->transactions()->latest()->get()); 
    }

    public function store(Request $request) {
        $data = $request->validate([
            'title' => 'required|string|min:1',
            'amount' => 'required|numeric|min:1',
            'type' => 'required|in:income,expense',
        ]);
        
        // Ganti auth()->id() jadi Auth::id()
        $data['user_id'] = Auth::id(); 
        return response()->json(Transaction::create($data));
    }

    public function update(Request $request, $id) {
        $data = $request->validate([
            'title' => 'required|string|min:1',
            'amount' => 'required|numeric|min:1',
            'type' => 'required|in:income,expense',
        ]);

        // Ganti auth()->user() jadi Auth::user()
        $transaction = Auth::user()->transactions()->findOrFail($id);
        $transaction->update($data);
        return response()->json($transaction);
    }

    public function destroy($id) {
        // Ganti auth()->user() jadi Auth::user()
        $transaction = Auth::user()->transactions()->findOrFail($id);
        $transaction->delete();
        return response()->json(['message' => 'Deleted']);
    }

    public function export() {
        return Excel::download(new TransactionExport, 'Laporan_Keuangan.xlsx');
    }
}