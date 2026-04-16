<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    // Tambahkan baris ini bro:
    protected $fillable = ['title', 'amount', 'type'];
}