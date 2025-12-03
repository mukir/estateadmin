<?php

namespace App\Http\Controllers;

use App\Models\Estate;
use App\Models\ServiceCharge;
use Illuminate\Http\Request;

class ServiceChargeController extends Controller
{
    public function index(Request $request)
    {
        $query = ServiceCharge::query();

        if ($estateId = $request->get('estate_id')) {
            $query->where('estate_id', $estateId);
        }

        if (! is_null($request->get('is_active'))) {
            $query->where('is_active', filter_var($request->get('is_active'), FILTER_VALIDATE_BOOLEAN));
        }

        return response()->json($query->orderBy('name')->get());
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'estate_id' => ['required', 'integer', 'exists:estates,id'],
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'amount' => ['required', 'numeric', 'min:0'],
            'billing_cycle' => ['nullable', 'string', 'max:50'],
            'is_mandatory' => ['boolean'],
            'is_active' => ['boolean'],
        ]);

        $estate = Estate::findOrFail($data['estate_id']);
        $charge = $estate->serviceCharges()->create($data);

        return response()->json($charge, 201);
    }

    public function update(Request $request, ServiceCharge $serviceCharge)
    {
        $data = $request->validate([
            'name' => ['sometimes', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'amount' => ['nullable', 'numeric', 'min:0'],
            'billing_cycle' => ['nullable', 'string', 'max:50'],
            'is_mandatory' => ['boolean'],
            'is_active' => ['boolean'],
        ]);

        $serviceCharge->update($data);

        return response()->json($serviceCharge->fresh());
    }

    public function destroy(ServiceCharge $serviceCharge)
    {
        $serviceCharge->delete();

        return response()->json(['message' => 'Service charge removed']);
    }
}
