<?php

namespace App\Models;

use App\Models\Concerns\BelongsToBusiness;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Resident extends Model
{
    use BelongsToBusiness;
    use HasFactory;

    protected $fillable = [
        'business_id',
        'estate_id',
        'house_id',
        'full_name',
        'email',
        'phone',
        'id_number',
        'resident_type',
        'status',
        'reminder_opt_out',
        'notes',
    ];

    public function estate()
    {
        return $this->belongsTo(Estate::class);
    }

    public function house()
    {
        return $this->belongsTo(House::class);
    }

    public function invoices()
    {
        return $this->hasMany(Invoice::class);
    }

    public function payments()
    {
        return $this->hasManyThrough(Payment::class, Invoice::class);
    }

    public function followUps()
    {
        return $this->hasMany(FollowUp::class);
    }

    public function attachToHouse(?House $house): void
    {
        $previousHouse = $this->house;
        $this->house()->associate($house);
        $this->save();

        if ($house) {
            $house->markOccupied(true);
        }

        if ($previousHouse && $previousHouse->id !== $house?->id) {
            $previousHouse->markOccupied(false);
        }
    }
}
