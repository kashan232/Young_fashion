<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Vendor extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'category',
        'status',
        'email',
        'phone',
        'address',
        'opening_balance',
        'payment_due_date',
        'payment_day',
        'credit_limit',
        'payment_terms',
        'bank_name',
        'account_title',
        'account_number',
        'bank_details',
    ];

    public function ledger()
    {
        return $this->hasOne(VendorLedger::class);
    }
}
