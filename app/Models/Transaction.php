<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    // Ini 'pintu' buat data masuk
    protected $fillable = ['title', 'amount', 'type'];
}