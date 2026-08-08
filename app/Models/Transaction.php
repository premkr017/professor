<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Transaction extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'account_id',
        'to_account_id',
        'category_id',
        'type',
        'amount',
        'description',
        'payee',
        'payment_method',
        'attachment',
        'transaction_date',
    ];

    protected $casts = [
        'transaction_date' => 'date',
        'amount' => 'decimal:2',
    ];

    public const PAYMENT_METHODS = [
        'cash'        => 'Cash',
        'bank'        => 'Bank Transfer',
        'upi'         => 'UPI',
        'debit_card'  => 'Debit Card',
        'credit_card' => 'Credit Card',
        'netbanking'  => 'Net Banking',
        'cheque'      => 'Cheque',
        'wallet'      => 'E-Wallet',
        'other'       => 'Other',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function account(): BelongsTo
    {
        return $this->belongsTo(Account::class);
    }

    public function toAccount(): BelongsTo
    {
        return $this->belongsTo(Account::class, 'to_account_id');
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }
}
