<?php

namespace App\Http\Controllers;

use App\Models\Estate;
use App\Models\House;
use App\Models\Resident;
use Illuminate\Http\Request;

class ResidentController extends Controller
{
    public function index(Request $request)
    {
        $query = Resident::query()->with(['estate', 'house']);

        if ($estateId = $request->get('estate_id')) {
            $query->where('estate_id', $estateId);
        }

        if ($status = $request->get('status')) {
            $query->where('status', $status);
        }

        $residents = $query->orderBy('full_name')->get();

        if ($request->wantsJson()) {
            return response()->json($residents);
        }

        $estates = Estate::orderBy('name')->get();
        $houses = House::orderBy('house_code')->get();

        return view('business.residents', compact('estates', 'houses', 'residents'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'estate_id' => ['required', 'integer', 'exists:estates,id'],
            'house_id' => ['nullable', 'integer', 'exists:houses,id'],
            'full_name' => ['required', 'string', 'max:255'],
            'email' => ['nullable', 'email', 'max:255'],
            'phone' => ['nullable', 'string', 'max:50'],
            'id_number' => ['nullable', 'string', 'max:50'],
            'resident_type' => ['nullable', 'string', 'max:50'],
            'status' => ['nullable', 'string', 'max:50'],
            'notes' => ['nullable', 'string'],
        ]);

        $estate = Estate::findOrFail($data['estate_id']);
        $house = null;

        if (! empty($data['house_id'])) {
            $house = House::where('estate_id', $estate->id)->findOrFail($data['house_id']);
        }

        $resident = Resident::create($data);

        if ($house) {
            $resident->attachToHouse($house);
        }

        $estate->refreshUnitCounters();

        return response()->json($resident->load(['estate', 'house']), 201);
    }

    public function show(Resident $resident)
    {
        return response()->json($resident->load(['estate', 'house']));
    }

    public function update(Request $request, Resident $resident)
    {
        $data = $request->validate([
            'house_id' => ['nullable', 'integer', 'exists:houses,id'],
            'full_name' => ['sometimes', 'string', 'max:255'],
            'email' => ['nullable', 'email', 'max:255'],
            'phone' => ['nullable', 'string', 'max:50'],
            'id_number' => ['nullable', 'string', 'max:50'],
            'resident_type' => ['nullable', 'string', 'max:50'],
            'status' => ['nullable', 'string', 'max:50'],
            'notes' => ['nullable', 'string'],
        ]);

        $previousHouse = $resident->house;

        $resident->update(array_diff_key($data, array_flip(['house_id'])));

        if (array_key_exists('house_id', $data)) {
            $house = $data['house_id']
                ? House::where('estate_id', $resident->estate_id)->findOrFail($data['house_id'])
                : null;

            $resident->attachToHouse($house);
        }

        if (! array_key_exists('house_id', $data)) {
            $resident->setRelation('house', $previousHouse);
        }

        if (isset($data['status']) && in_array($data['status'], ['moved_out', 'inactive'], true)) {
            $houseToClear = $resident->house ?? $previousHouse;

            if ($houseToClear) {
                $houseToClear->markOccupied(false);
                $resident->house()->dissociate()->save();
            }
        }

        $resident->estate?->refreshUnitCounters();

        return response()->json($resident->load(['estate', 'house']));
    }

    public function destroy(Resident $resident)
    {
        $estate = $resident->estate;
        $house = $resident->house;
        $resident->delete();

        if ($house) {
            $house->markOccupied(false);
        }

        $estate?->refreshUnitCounters();

        return response()->json(['message' => 'Resident removed']);
    }
}
