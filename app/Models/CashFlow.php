<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CashFlow extends Model
{
    use HasFactory;

    protected $table = 'cash_flows';

    // Kolom yang diizinkan untuk diisi data secara massal
    protected $fillable = [
        'transaction_id',
        'project_id',
        'type',
        'amount',
        'category',
        'transaction_date',
        'description',
    ];
}