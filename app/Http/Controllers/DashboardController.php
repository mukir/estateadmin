<?php

namespace App\Http\Controllers;

use App\Models\Estate;
use App\Models\House;
use App\Models\Invoice;
use App\Models\Payment;
use App\Models\Resident;
use App\Models\ServiceCharge;
use App\Support\BusinessContext;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function show(Request $request)
    {
        $business = BusinessContext::get();

        $estates = Estate::count();
        $houses = House::count();
        $occupied = House::where('is_occupied', true)->count();
        $arrears = Invoice::where('balance', '>', 0)->sum('balance');
        $collections = Payment::where('status', 'confirmed')->sum('amount');
        $serviceCharges = ServiceCharge::count();
        $residentCount = Resident::count();
        $invoiceCount = Invoice::count();

        $data = [
            'business' => $business,
            'estate_count' => $estates,
            'house_count' => $houses,
            'occupied_units' => $occupied,
            'vacant_units' => max($houses - $occupied, 0),
            'arrears_total' => $arrears,
            'collections_total' => $collections,
            'service_charge_count' => $serviceCharges,
            'resident_count' => $residentCount,
            'invoice_count' => $invoiceCount,
        ];

        if ($request->wantsJson()) {
            return response()->json($data);
        }

        return view('business.dashboard', $data);
    }
}
