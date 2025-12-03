<?php

namespace App\Models;

use App\Models\Concerns\BelongsToBusiness;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FollowUp extends Model
{
    use BelongsToBusiness;
    use HasFactory;

    protected $fillable = [
        'business_id',
        'resident_id',
        'invoice_id',
        'user_id',
        'channel',
        'status_tag',
        'next_action_date',
        'notes',
    ];

    protected $casts = [
        'next_action_date' => 'date',
    ];

    public function resident()
    {
        return $this->belongsTo(Resident::class);
    }

    public function invoice()
    {
        return $this->belongsTo(Invoice::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
