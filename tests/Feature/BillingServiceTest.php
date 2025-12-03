<?php

namespace Tests\Feature;

use App\Models\Business;
use App\Models\Estate;
use App\Models\House;
use App\Models\Invoice;
use App\Models\Resident;
use App\Models\ServiceCharge;
use App\Services\BillingService;
use App\Support\BusinessContext;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Tests\TestCase;

class BillingServiceTest extends TestCase
{
    use RefreshDatabase;

    public function test_monthly_billing_creates_invoices_with_items(): void
    {
        $business = Business::create([
            'name' => 'Biz',
            'slug' => 'biz',
            'status' => 'active',
            'plan' => 'basic',
        ]);

        BusinessContext::set($business);

        $estate = Estate::create(['name' => 'Estate 1']);
        $house = House::create([
            'estate_id' => $estate->id,
            'house_code' => 'A-1',
            'is_occupied' => true,
            'default_service_charge' => 5000,
        ]);

        $resident = Resident::create([
            'estate_id' => $estate->id,
            'house_id' => $house->id,
            'full_name' => 'Jane Doe',
            'status' => 'active',
        ]);

        $house->markOccupied(true);

        ServiceCharge::create([
            'estate_id' => $estate->id,
            'name' => 'Garbage',
            'amount' => 500,
            'billing_cycle' => 'monthly',
        ]);

        $service = $this->app->make(BillingService::class);

        $result = $service->runMonthlyBilling(Carbon::parse('2025-11-05'));

        $this->assertEquals('2025-11', $result['period']);
        $this->assertEquals(1, $result['invoices_created']);

        $invoice = Invoice::first();

        $this->assertNotNull($invoice);
        $this->assertEquals('2025-11', $invoice->billing_period);
        $this->assertEquals($business->id, $invoice->business_id);
        $this->assertEquals(2, $invoice->items()->count()); // Garbage + default service charge
        $this->assertEquals(5500, (float) $invoice->total_amount);
        $this->assertEquals(5500, (float) $invoice->balance);
    }
}
