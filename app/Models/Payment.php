<?php

namespace App\Models;

use App\Models\Concerns\BelongsToBusiness;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    use BelongsToBusiness;
    use HasFactory;

    protected $fillable = [
        'business_id',
        'invoice_id',
        'payment_date',
        'amount',
        'method',
        'reference',
        'status',
        'notes',
    ];

    protected $casts = [
        'payment_date' => 'date',
        'amount' => 'decimal:2',
    ];

    protected static function booted(): void
    {
        static::saved(function (Payment $payment): void {
            $payment->invoice?->recalculateTotals();
        });

        static::deleted(function (Payment $payment): void {
            $payment->invoice?->recalculateTotals();
        });
    }

    public function invoice()
    {
        return $this->belongsTo(Invoice::class);
    }
}
