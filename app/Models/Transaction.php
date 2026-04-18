<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    use HasFactory;

    // Tambahkan transaction_date di sini
    protected $fillable = ['title', 'amount', 'type', 'category', 'transaction_date', 'user_id'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}