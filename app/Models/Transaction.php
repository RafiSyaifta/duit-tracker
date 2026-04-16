<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class Transaction extends Model {
    // user_id WAJIB masuk sini biar bisa disimpan
    protected $fillable = ['title', 'amount', 'type', 'user_id'];

    public function user() {
        return $this->belongsTo(User::class);
    }
}