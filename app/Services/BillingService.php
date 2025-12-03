<?php

namespace App\Services;

use App\Models\Business;
use App\Models\Invoice;
use App\Support\BusinessContext;
use Illuminate\Support\Carbon;

class BillingService
{
    public function runMonthlyBilling(?Carbon $billingDate = null): array
    {
        $date = $billingDate ?? now();
        $period = $date->format('Y-m');
        $created = 0;
        $dueDays = 7;

        Business::where('status', '!=', 'suspended')
            ->get()
            ->each(function (Business $business) use ($period, $date, $dueDays, &$created) {
                BusinessContext::set($business);

                $business->estates()
                    ->where('is_active', true)
                    ->get()
                    ->each(function ($estate) use ($period, $date, $dueDays, &$created) {
                        $houses = $estate->houses()
                            ->where('is_active', true)
                            ->where('is_occupied', true)
                            ->get();

                        $charges = $estate->serviceCharges()->where('is_active', true)->get();

                        foreach ($houses as $house) {
                            $resident = $house->currentResident();

                            if (! $resident) {
                                continue;
                            }

                            $invoice = Invoice::firstOrCreate(
                                [
                                    'estate_id' => $estate->id,
                                    'house_id' => $house->id,
                                    'resident_id' => $resident->id,
                                    'billing_period' => $period,
                                ],
                                [
                                    'invoice_date' => $date->toDateString(),
                                    'due_date' => $date->copy()->addDays($dueDays)->toDateString(),
                                    'status' => 'sent',
                                ]
                            );

                            if (! $invoice->wasRecentlyCreated) {
                                $invoice->recalculateTotals();
                                continue;
                            }

                            foreach ($charges as $charge) {
                                $invoice->items()->create([
                                    'service_charge_id' => $charge->id,
                                    'description' => $charge->name,
                                    'amount' => $charge->amount,
                                    'quantity' => 1,
                                ]);
                            }

                            if ($house->default_service_charge > 0 && $charges->where('name', 'Service Charge')->isEmpty()) {
                                $invoice->items()->create([
                                    'description' => 'Service Charge',
                                    'amount' => $house->default_service_charge,
                                    'quantity' => 1,
                                ]);
                            }

                            $invoice->recalculateTotals();
                            $created++;
                        }
                    });
            });

        BusinessContext::forget();

        return [
            'period' => $period,
            'invoices_created' => $created,
        ];
    }
}
