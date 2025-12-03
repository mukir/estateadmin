<?php

namespace App\Models;

use App\Models\Concerns\BelongsToBusiness;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Estate extends Model
{
    use BelongsToBusiness;
    use HasFactory;

    protected $fillable = [
        'business_id',
        'name',
        'code',
        'type',
        'county',
        'town',
        'ward',
        'address',
        'planned_units',
        'occupied_units',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function houses()
    {
        return $this->hasMany(House::class);
    }

    public function residents()
    {
        return $this->hasMany(Resident::class);
    }

    public function serviceCharges()
    {
        return $this->hasMany(ServiceCharge::class);
    }

    public function invoices()
    {
        return $this->hasMany(Invoice::class);
    }

    public function refreshUnitCounters(): void
    {
        $this->planned_units = $this->houses()->count();
        $this->occupied_units = $this->houses()->where('is_occupied', true)->count();
        $this->saveQuietly();
    }
}
