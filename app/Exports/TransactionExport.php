<?php

namespace App\Exports;

use App\Models\Transaction;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class TransactionExport implements FromCollection, WithHeadings, WithMapping
{
    public function collection()
    {
        // Ambil semua data dari database
        return Transaction::all(); 
    }

    // Kasih judul kolom di baris pertama Excel
    public function headings(): array
    {
        return ['ID', 'Keterangan', 'Nominal', 'Tipe', 'Tanggal'];
    }

    // Atur format datanya biar enak dibaca
    public function map($transaction): array
    {
        return [
            $transaction->id,
            $transaction->title,
            $transaction->amount,
            $transaction->type == 'income' ? 'Pemasukan' : 'Pengeluaran',
            $transaction->created_at->format('d-m-Y'),
        ];
    }

    
}