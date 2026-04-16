<?php
namespace App\Http\Controllers;

use App\Models\Transaction;
use Illuminate\Http\Request;
use App\Exports\TransactionExport;
use Maatwebsite\Excel\Facades\Excel;

class TransactionController extends Controller
{
    public function index() { 
        // Cuma ambil punya user yang login
        return response()->json(auth()->user()->transactions()->latest()->get()); 
    }

    public function store(Request $request) {
        $data = $request->validate([
            'title' => 'required',
            'amount' => 'required|numeric',
            'type' => 'required|in:income,expense',
        ]);
        
        $data['user_id'] = auth()->id(); // Isi user_id otomatis dari sistem login
        return response()->json(Transaction::create($data));
    }

    public function update(Request $request, $id) {
        $data = $request->validate([
            'title' => 'required', 'amount' => 'required|numeric', 'type' => 'required|in:income,expense',
        ]);

        // findOrFail milik user yang login (biar user lain ga bisa bajak ID)
        $transaction = auth()->user()->transactions()->findOrFail($id);
        $transaction->update($data);
        return response()->json($transaction);
    }

    public function destroy($id) {
        $transaction = auth()->user()->transactions()->findOrFail($id);
        $transaction->delete();
        return response()->json(['message' => 'Deleted']);
    }

    public function export() {
        return Excel::download(new TransactionExport, 'Laporan_Keuangan.xlsx');
    }
}