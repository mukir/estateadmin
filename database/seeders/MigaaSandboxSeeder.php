<?php

namespace Database\Seeders;

use App\Models\Business;
use App\Models\Estate;
use App\Models\House;
use App\Models\Invoice;
use App\Models\InvoiceItem;
use App\Models\Payment;
use App\Models\Resident;
use App\Models\Role;
use App\Models\ServiceCharge;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class MigaaSandboxSeeder extends Seeder
{
    public function run(): void
    {
        $business = Business::updateOrCreate(
            ['slug' => 'migaa-golf-estate'],
            [
                'name' => 'Migaa Golf Estate',
                'contact_email' => 'migaresidents@gmail.com',
                'contact_phone' => '+254700000000',
                'status' => 'active',
                'plan' => 'standard',
                'trial_ends_at' => null,
            ]
        );

        $roles = collect(['admin', 'manager', 'accountant', 'viewer'])
            ->mapWithKeys(fn ($r) => [$r => Role::firstOrCreate(['business_id' => $business->id, 'name' => $r])]);

        $user = User::updateOrCreate(
            ['email' => 'migaaadmin@estateadmin.test'],
            [
                'business_id' => $business->id,
                'name' => 'Migaa Admin',
                'password' => Hash::make('Password123!'),
                'is_business_owner' => true,
                'status' => 'active',
            ]
        );
        $user->roles()->syncWithoutDetaching([
            $roles['admin']->id => ['business_id' => $business->id],
        ]);

        $estate1 = Estate::updateOrCreate(
            ['business_id' => $business->id, 'code' => 'MIG-P1'],
            [
                'name' => 'Migaa Golf Estate - Phase 1',
                'type' => 'Residential',
                'address' => 'Ting’ang’a ward, Kiambu County',
                'planned_units' => 0,
                'occupied_units' => 0,
                'is_active' => true,
            ]
        );

        $estate2 = Estate::updateOrCreate(
            ['business_id' => $business->id, 'code' => 'MIG-P2'],
            [
                'name' => 'Migaa Golf Estate - Phase 2',
                'type' => 'Residential',
                'address' => 'Ting’ang’a ward, Kiambu County',
                'planned_units' => 0,
                'occupied_units' => 0,
                'is_active' => true,
            ]
        );

        $houses = [
            ['estate' => $estate1, 'code' => 'MIG-P1-A1', 'block' => 'A', 'type' => '4BR', 'charge' => 5000, 'occupied' => true],
            ['estate' => $estate1, 'code' => 'MIG-P1-A2', 'block' => 'A', 'type' => '4BR', 'charge' => 5000, 'occupied' => true],
            ['estate' => $estate1, 'code' => 'MIG-P1-B1', 'block' => 'B', 'type' => '3BR', 'charge' => 4000, 'occupied' => false],
            ['estate' => $estate2, 'code' => 'MIG-P2-C1', 'block' => 'C', 'type' => '3BR', 'charge' => 4200, 'occupied' => true],
            ['estate' => $estate2, 'code' => 'MIG-P2-C2', 'block' => 'C', 'type' => '3BR', 'charge' => 4200, 'occupied' => true],
        ];

        $houseModels = collect($houses)->map(function ($h) use ($business) {
            return House::updateOrCreate(
                ['business_id' => $business->id, 'house_code' => $h['code']],
                [
                    'estate_id' => $h['estate']->id,
                    'block' => $h['block'],
                    'house_type' => $h['type'],
                    'default_service_charge' => $h['charge'],
                    'is_occupied' => $h['occupied'],
                    'is_active' => true,
                ]
            );
        })->keyBy('house_code');

        $residents = [
            ['name' => 'Grace Mwangi', 'email' => 'grace.mwangi@example.com', 'phone' => '+254711000001', 'house' => 'MIG-P1-A1'],
            ['name' => 'Peter Njoroge', 'email' => 'peter.njoroge@example.com', 'phone' => '+254722000002', 'house' => 'MIG-P1-A2'],
            ['name' => 'Asha Kamau', 'email' => 'asha.kamau@example.com', 'phone' => '+254733000003', 'house' => 'MIG-P2-C1'],
            ['name' => 'Brian Otieno', 'email' => 'brian.otieno@example.com', 'phone' => '+254744000004', 'house' => 'MIG-P2-C2'],
        ];

        $residentModels = collect($residents)->map(function ($r) use ($business, $houseModels) {
            $house = $houseModels[$r['house']];
            return Resident::updateOrCreate(
                ['business_id' => $business->id, 'email' => $r['email']],
                [
                    'estate_id' => $house->estate_id,
                    'house_id' => $house->id,
                    'full_name' => $r['name'],
                    'phone' => $r['phone'],
                    'resident_type' => 'Owner',
                    'status' => 'active',
                ]
            );
        })->keyBy('email');

        foreach ([$estate1, $estate2] as $estate) {
            $estate->serviceCharges()->updateOrCreate(
                ['name' => 'Service Charge'],
                ['business_id' => $business->id, 'amount' => 2500, 'billing_cycle' => 'monthly', 'is_active' => true]
            );
            $estate->serviceCharges()->updateOrCreate(
                ['name' => 'Garbage'],
                ['business_id' => $business->id, 'amount' => 500, 'billing_cycle' => 'monthly', 'is_active' => true]
            );
        }

        $invoiceDate = Carbon::parse('first day of this month');
        $dueDate = $invoiceDate->copy()->addDays(7);

        $residentModels->each(function (Resident $resident) use ($business, $invoiceDate, $dueDate) {
            $house = $resident->house;
            $charges = $house->estate->serviceCharges()->get();

            $invoice = Invoice::create([
                'business_id' => $business->id,
                'estate_id' => $house->estate_id,
                'house_id' => $house->id,
                'resident_id' => $resident->id,
                'billing_period' => $invoiceDate->format('Y-m'),
                'invoice_date' => $invoiceDate->toDateString(),
                'due_date' => $dueDate->toDateString(),
                'status' => 'sent',
            ]);

            foreach ($charges as $charge) {
                InvoiceItem::create([
                    'business_id' => $business->id,
                    'invoice_id' => $invoice->id,
                    'service_charge_id' => $charge->id,
                    'description' => $charge->name,
                    'amount' => $charge->amount,
                    'quantity' => 1,
                ]);
            }

            if ($house->default_service_charge > 0) {
                InvoiceItem::create([
                    'business_id' => $business->id,
                    'invoice_id' => $invoice->id,
                    'description' => 'House service charge',
                    'amount' => $house->default_service_charge,
                    'quantity' => 1,
                ]);
            }

            $invoice->recalculateTotals();
            $invoice->generateReference();

            // Make one invoice partially paid to show arrears
            if ($resident->email === 'grace.mwangi@example.com') {
                Payment::create([
                    'business_id' => $business->id,
                    'invoice_id' => $invoice->id,
                    'payment_date' => $invoiceDate->toDateString(),
                    'amount' => $invoice->total_amount / 2,
                    'method' => 'M-Pesa',
                    'reference' => 'MPESA-'.substr(uniqid(), -6),
                    'status' => 'confirmed',
                ]);
                $invoice->refresh();
                $invoice->recalculateTotals();
            }
        });

        $estate1->refreshUnitCounters();
        $estate2->refreshUnitCounters();
    }
}
