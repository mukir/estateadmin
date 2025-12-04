<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class SuperAdminSeeder extends Seeder
{
    public function run(): void
    {
        $email = env('SEED_SUPERADMIN_EMAIL', 'superadmin@estateadmin.test');
        $password = env('SEED_SUPERADMIN_PASSWORD', 'Password123!');

        User::updateOrCreate(
            ['email' => $email],
            [
                'name' => 'Super Admin',
                'password' => Hash::make($password),
                'business_id' => null,
                'is_business_owner' => false,
                'status' => 'active',
            ]
        );
    }
}
