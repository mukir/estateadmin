<?php

namespace App\Models;

use App\Models\Concerns\BelongsToBusiness;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class House extends Model
{
    use BelongsToBusiness;
    use HasFactory;

    protected $fillable = [
        'business_id',
        'estate_id',
        'house_code',
        'block',
        'house_type',
        'default_service_charge',
        'is_occupied',
        'is_active',
    ];

    protected $casts = [
        'default_service_charge' => 'decimal:2',
        'is_occupied' => 'boolean',
        'is_active' => 'boolean',
    ];

    public function estate()
    {
        return $this->belongsTo(Estate::class);
    }

    public function residents()
    {
        return $this->hasMany(Resident::class);
    }

    public function invoices()
    {
        return $this->hasMany(Invoice::class);
    }

    public function currentResident()
    {
        return $this->residents()->where('status', 'active')->latest()->first();
    }

    public function markOccupied(bool $occupied): void
    {
        $this->is_occupied = $occupied;
        $this->saveQuietly();
        $this->estate?->refreshUnitCounters();
    }
}
