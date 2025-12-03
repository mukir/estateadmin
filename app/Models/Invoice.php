<?php

namespace App\Models;

use App\Models\Concerns\BelongsToBusiness;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Invoice extends Model
{
    use BelongsToBusiness;
    use HasFactory;

    protected $fillable = [
        'business_id',
        'estate_id',
        'house_id',
        'resident_id',
        'billing_period',
        'invoice_date',
        'due_date',
        'reference',
        'total_amount',
        'amount_paid',
        'balance',
        'status',
    ];

    protected $casts = [
        'invoice_date' => 'date',
        'due_date' => 'date',
        'total_amount' => 'decimal:2',
        'amount_paid' => 'decimal:2',
        'balance' => 'decimal:2',
    ];

    public function estate()
    {
        return $this->belongsTo(Estate::class);
    }

    public function house()
    {
        return $this->belongsTo(House::class);
    }

    public function resident()
    {
        return $this->belongsTo(Resident::class);
    }

    public function items()
    {
        return $this->hasMany(InvoiceItem::class);
    }

    public function payments()
    {
        return $this->hasMany(Payment::class);
    }

    public function followUps()
    {
        return $this->hasMany(FollowUp::class);
    }

    public function generateReference(): void
    {
        if ($this->reference) {
            return;
        }

        $prefix = 'INV-'.now()->format('ymd');
        $random = strtoupper(substr(uniqid(), -5));
        $this->reference = $prefix.'-'.$random;
        $this->saveQuietly();
    }

    public function markSent(): void
    {
        $this->status = 'sent';
        if (! $this->invoice_date) {
            $this->invoice_date = now()->toDateString();
        }
        $this->saveQuietly();
    }

    public function recalculateTotals(): void
    {
        $itemTotal = $this->items()
            ->select(DB::raw('SUM(amount * quantity) as total'))
            ->value('total') ?? 0;

        $paid = $this->payments()
            ->where('status', 'confirmed')
            ->sum('amount');

        $this->total_amount = $itemTotal;
        $this->amount_paid = $paid;
        $this->balance = $itemTotal - $paid;

        if ($this->balance <= 0) {
            $this->status = 'paid';
        } elseif ($paid > 0) {
            $this->status = 'partial';
        } elseif ($this->status === 'draft') {
            $this->status = 'draft';
        } else {
            $this->status = 'sent';
        }

        $this->saveQuietly();
    }
}
