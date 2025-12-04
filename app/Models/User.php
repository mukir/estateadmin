<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'business_id',
        'name',
        'email',
        'password',
        'is_business_owner',
        'status',
        'carry_forward_enabled',
        'mfa_enabled',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'is_business_owner' => 'boolean',
            'mfa_enabled' => 'boolean',
            'carry_forward_enabled' => 'boolean',
        ];
    }

    public function business()
    {
        return $this->belongsTo(Business::class);
    }

    public function roles()
    {
        return $this->belongsToMany(Role::class)
            ->withPivot('business_id')
            ->withTimestamps();
    }

    public function hasRole(string $name): bool
    {
        return $this->roles->contains('name', $name);
    }

    public function assignRole(Role|string $role): void
    {
        $roleModel = $role instanceof Role
            ? $role
            : Role::where('business_id', $this->business_id)->where('name', $role)->first();

        if ($roleModel) {
            $this->roles()->syncWithoutDetaching([
                $roleModel->id => ['business_id' => $this->business_id],
            ]);
        }
    }

    public function isSuperAdmin(): bool
    {
        return $this->business_id === null;
    }

    public function requiresMfa(): bool
    {
        return (bool) $this->mfa_enabled;
    }
}
