<?php

namespace Tests\Feature;

use App\Models\Business;
use App\Models\Estate;
use App\Support\BusinessContext;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TenancyScopeTest extends TestCase
{
    use RefreshDatabase;

    public function test_global_scope_filters_by_business_context(): void
    {
        $businessA = Business::create([
            'name' => 'Biz A',
            'slug' => 'biz-a',
            'status' => 'active',
            'plan' => 'basic',
        ]);

        $businessB = Business::create([
            'name' => 'Biz B',
            'slug' => 'biz-b',
            'status' => 'active',
            'plan' => 'basic',
        ]);

        BusinessContext::set($businessA);
        $estateA = Estate::create(['name' => 'Estate A']);

        BusinessContext::set($businessB);
        $estateB = Estate::create(['name' => 'Estate B']);

        BusinessContext::set($businessA);

        $this->assertSame($businessA->id, $estateA->business_id);
        $this->assertSame($businessB->id, $estateB->business_id);
        $this->assertCount(1, Estate::all());
        $this->assertEquals('Estate A', Estate::first()->name);
    }
}
