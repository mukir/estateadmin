<?php

namespace App\Http\Controllers;

use App\Models\Estate;
use App\Models\House;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class HouseController extends Controller
{
    public function index(Request $request)
    {
        $query = House::query()->with('estate');

        if ($estateId = $request->get('estate_id')) {
            $query->where('estate_id', $estateId);
        }

        if (! is_null($request->get('is_occupied'))) {
            $query->where('is_occupied', filter_var($request->get('is_occupied'), FILTER_VALIDATE_BOOLEAN));
        }

        $houses = $query->orderBy('house_code')->get();

        if ($request->wantsJson()) {
            return response()->json($houses);
        }

        $estates = Estate::orderBy('name')->get();

        return view('business.houses', compact('estates', 'houses'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'estate_id' => ['required', 'integer', 'exists:estates,id'],
            'house_code' => ['required', 'string', 'max:100'],
            'block' => ['nullable', 'string', 'max:100'],
            'house_type' => ['nullable', 'string', 'max:100'],
            'default_service_charge' => ['nullable', 'numeric', 'min:0'],
            'is_active' => ['boolean'],
        ]);

        $estate = Estate::findOrFail($data['estate_id']);

        $house = $estate->houses()->create($data);
        $estate->refreshUnitCounters();

        return response()->json($house, 201);
    }

    public function show(House $house)
    {
        return response()->json($house->load(['estate', 'residents']));
    }

    public function update(Request $request, House $house)
    {
        $data = $request->validate([
            'house_code' => ['sometimes', 'string', 'max:100'],
            'block' => ['nullable', 'string', 'max:100'],
            'house_type' => ['nullable', 'string', 'max:100'],
            'default_service_charge' => ['nullable', 'numeric', 'min:0'],
            'is_active' => ['boolean'],
            'is_occupied' => ['boolean'],
        ]);

        $house->update($data);
        $house->estate?->refreshUnitCounters();

        return response()->json($house->fresh());
    }

    public function destroy(House $house)
    {
        $estate = $house->estate;
        $house->delete();
        $estate?->refreshUnitCounters();

        return response()->json(['message' => 'House removed']);
    }

    public function import(Request $request)
    {
        $data = $request->validate([
            'estate_id' => ['required', 'integer', 'exists:estates,id'],
            'houses' => ['required', 'array'],
            'houses.*.house_code' => ['required', 'string'],
            'houses.*.block' => ['nullable', 'string'],
            'houses.*.house_type' => ['nullable', 'string'],
            'houses.*.default_service_charge' => ['nullable', 'numeric', 'min:0'],
        ]);

        $estate = Estate::findOrFail($data['estate_id']);

        $created = 0;
        foreach ($data['houses'] as $payload) {
            if (! isset($payload['house_code'])) {
                continue;
            }

            $exists = $estate->houses()
                ->where('house_code', $payload['house_code'])
                ->exists();

            if ($exists) {
                continue;
            }

            $estate->houses()->create([
                'house_code' => $payload['house_code'],
                'block' => $payload['block'] ?? null,
                'house_type' => $payload['house_type'] ?? null,
                'default_service_charge' => $payload['default_service_charge'] ?? 0,
            ]);

            $created++;
        }

        if ($created === 0) {
            throw ValidationException::withMessages(['houses' => 'No new houses were imported.']);
        }

        $estate->refreshUnitCounters();

        return response()->json([
            'message' => 'Houses imported',
            'created' => $created,
        ], 201);
    }
}
