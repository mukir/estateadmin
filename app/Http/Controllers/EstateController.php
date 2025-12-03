<?php

namespace App\Http\Controllers;

use App\Models\Estate;
use Illuminate\Http\Request;

class EstateController extends Controller
{
    public function index(Request $request)
    {
        $query = Estate::query();

        if ($request->boolean('active_only')) {
            $query->where('is_active', true);
        }

        $estates = $query->orderBy('name')->get();

        if ($request->wantsJson()) {
            return response()->json($estates);
        }

        return view('business.estates', compact('estates'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'code' => ['nullable', 'string', 'max:50'],
            'type' => ['nullable', 'string', 'max:100'],
            'county' => ['nullable', 'string', 'max:100'],
            'town' => ['nullable', 'string', 'max:100'],
            'ward' => ['nullable', 'string', 'max:100'],
            'address' => ['nullable', 'string', 'max:255'],
            'is_active' => ['boolean'],
        ]);

        $estate = Estate::create($data);
        $estate->refreshUnitCounters();

        return response()->json($estate, 201);
    }

    public function show(Estate $estate)
    {
        return response()->json($estate);
    }

    public function update(Request $request, Estate $estate)
    {
        $data = $request->validate([
            'name' => ['sometimes', 'string', 'max:255'],
            'code' => ['nullable', 'string', 'max:50'],
            'type' => ['nullable', 'string', 'max:100'],
            'county' => ['nullable', 'string', 'max:100'],
            'town' => ['nullable', 'string', 'max:100'],
            'ward' => ['nullable', 'string', 'max:100'],
            'address' => ['nullable', 'string', 'max:255'],
            'is_active' => ['boolean'],
        ]);

        $estate->update($data);
        $estate->refreshUnitCounters();

        return response()->json($estate->fresh());
    }

    public function destroy(Estate $estate)
    {
        $estate->delete();

        return response()->json(['message' => 'Estate removed']);
    }
}
