<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB; // Wajib untuk menjalankan perintah SQL manual

return new class extends Migration
{
    public function up(): void
    {
        // 1. Tambah kolom transaction_date
        Schema::table('transactions', function (Blueprint $table) {
            $table->date('transaction_date')->nullable()->after('type');
        });

        // 2. Sihir Pro: Copy tanggal dari created_at ke kolom baru untuk data yang udah ada
        DB::statement('UPDATE transactions SET transaction_date = DATE(created_at) WHERE transaction_date IS NULL');
    }

    public function down(): void
    {
        Schema::table('transactions', function (Blueprint $table) {
            $table->dropColumn('transaction_date');
        });
    }
};