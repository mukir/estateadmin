<?php

namespace Database\Seeders;

use App\Models\Business;
use App\Models\Estate;
use App\Models\House;
use App\Models\Invoice;
use App\Models\InvoiceItem;
use App\Models\Resident;
use App\Models\ServiceCharge;
use App\Support\BusinessContext;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class MigaaDemoSeeder extends Seeder
{
    public function run(): void
    {
        $business = Business::first() ?? Business::create([
            'name' => 'Migaa Demo',
            'slug' => 'migaa-demo',
            'contact_email' => 'demo@example.com',
            'status' => 'active',
        ]);

        BusinessContext::set($business);

        $estate = Estate::firstOrCreate(
            ['business_id' => $business->id, 'code' => 'MIGAA-CENTRAL'],
            ['name' => 'Migaa Golf Estate', 'type' => 'Gated Community', 'planned_units' => 10, 'address' => 'Kiambu']
        );

        $houses = [
            ['house_code' => 'A-101', 'block' => 'A', 'house_type' => '3BR', 'default_service_charge' => 5000, 'is_occupied' => true],
            ['house_code' => 'A-102', 'block' => 'A', 'house_type' => '2BR', 'default_service_charge' => 4500, 'is_occupied' => true],
            ['house_code' => 'B-201', 'block' => 'B', 'house_type' => '2BR', 'default_service_charge' => 4500, 'is_occupied' => false],
        ];

        foreach ($houses as $data) {
            $estate->houses()->firstOrCreate(
                ['house_code' => $data['house_code']],
                $data
            );
        }

        $serviceCharges = [
            ['name' => 'Security', 'amount' => 1500, 'billing_cycle' => 'monthly'],
            ['name' => 'Garbage Collection', 'amount' => 800, 'billing_cycle' => 'monthly'],
        ];

        foreach ($serviceCharges as $charge) {
            ServiceCharge::firstOrCreate(
                ['estate_id' => $estate->id, 'name' => $charge['name']],
                $charge + ['business_id' => $business->id]
            );
        }

        $residents = [
            ['full_name' => 'Derrick Mushangi', 'email' => 'dmushangi@gmail.com', 'phone' => '+254724147772', 'house_code' => 'A-101'],
            ['full_name' => 'Wanjau Kev', 'email' => 'wanjaukev@gmail.com', 'phone' => '+254700000000', 'house_code' => 'A-102'],
        ];

        foreach ($residents as $data) {
            $house = House::where('estate_id', $estate->id)->where('house_code', $data['house_code'])->first();
            $resident = Resident::firstOrCreate(
                ['email' => $data['email'] ?? Str::slug($data['full_name']).'@example.com'],
                [
                    'estate_id' => $estate->id,
                    'house_id' => $house?->id,
                    'full_name' => $data['full_name'],
                    'phone' => $data['phone'] ?? null,
                    'resident_type' => 'owner',
                    'status' => 'active',
                ]
            );

            if ($house) {
                $resident->attachToHouse($house);
            }
        }

        // Create a sample invoice for first resident
        $resident = Resident::where('estate_id', $estate->id)->first();
        $house = $resident?->house;
        if ($resident && $house) {
            $invoice = Invoice::firstOrCreate(
                [
                    'estate_id' => $estate->id,
                    'house_id' => $house->id,
                    'resident_id' => $resident->id,
                    'billing_period' => now()->format('Y-m'),
                ],
                [
                    'invoice_date' => now()->toDateString(),
                    'due_date' => now()->addDays(7)->toDateString(),
                    'status' => 'sent',
                ]
            );

            if ($invoice->wasRecentlyCreated) {
                InvoiceItem::create([
                    'business_id' => $business->id,
                    'invoice_id' => $invoice->id,
                    'description' => 'Service charge',
                    'amount' => $house->default_service_charge,
                    'quantity' => 1,
                ]);

                foreach ($serviceCharges as $charge) {
                    InvoiceItem::create([
                        'business_id' => $business->id,
                        'invoice_id' => $invoice->id,
                        'description' => $charge['name'],
                        'amount' => $charge['amount'],
                        'quantity' => 1,
                    ]);
                }

                $invoice->recalculateTotals();
                $invoice->generateReference();
                $invoice->markSent();
            }
        }

        $estate->refreshUnitCounters();
    }
}
