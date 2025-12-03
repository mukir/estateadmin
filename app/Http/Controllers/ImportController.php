<?php

namespace App\Http\Controllers;

use App\Models\Estate;
use App\Models\House;
use App\Models\Resident;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class ImportController extends Controller
{
    public function template(string $type)
    {
        $headers = [
            'estates' => ['name', 'code', 'type', 'address', 'planned_units'],
            'houses' => ['estate_code', 'house_code', 'block', 'house_type', 'default_service_charge', 'is_occupied'],
            'residents' => ['estate_code', 'house_code', 'full_name', 'email', 'phone', 'resident_type', 'status'],
        ];

        if (! isset($headers[$type])) {
            abort(404);
        }

        $filename = $type.'-import-template.csv';
        $content = implode(',', $headers[$type])."\n";

        return response($content, 200, [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ]);
    }

    public function importEstates(Request $request)
    {
        $rows = $this->parseCsv($request);

        $created = 0;
        foreach ($rows as $row) {
            if (! $row['name']) {
                continue;
            }

            $exists = Estate::where('name', $row['name'])
                ->orWhere('code', $row['code'])
                ->exists();

            if ($exists) {
                continue;
            }

            Estate::create([
                'name' => $row['name'],
                'code' => $row['code'] ?: null,
                'type' => $row['type'] ?: null,
                'address' => $row['address'] ?: null,
                'planned_units' => $row['planned_units'] ?: null,
            ]);
            $created++;
        }

        if ($created === 0) {
            throw ValidationException::withMessages(['file' => 'No estates were imported. Check headers or duplicates.']);
        }

        return back()->with('status', "Imported {$created} estate(s).");
    }

    public function importHouses(Request $request)
    {
        $rows = $this->parseCsv($request);

        $created = 0;
        foreach ($rows as $row) {
            if (! $row['house_code'] || ! $row['estate_code']) {
                continue;
            }

            $estate = Estate::where('code', $row['estate_code'])->first();

            if (! $estate) {
                continue;
            }

            $exists = $estate->houses()->where('house_code', $row['house_code'])->exists();
            if ($exists) {
                continue;
            }

            $estate->houses()->create([
                'house_code' => $row['house_code'],
                'block' => $row['block'] ?: null,
                'house_type' => $row['house_type'] ?: null,
                'default_service_charge' => $row['default_service_charge'] ?: 0,
                'is_occupied' => Str::lower($row['is_occupied'] ?? '') === 'yes',
            ]);
            $estate->refreshUnitCounters();
            $created++;
        }

        if ($created === 0) {
            throw ValidationException::withMessages(['file' => 'No houses were imported. Check headers, estate codes, or duplicates.']);
        }

        return back()->with('status', "Imported {$created} house(s).");
    }

    public function importResidents(Request $request)
    {
        $rows = $this->parseCsv($request);

        $created = 0;
        foreach ($rows as $row) {
            if (! $row['full_name'] || ! $row['estate_code']) {
                continue;
            }

            $estate = Estate::where('code', $row['estate_code'])->first();
            if (! $estate) {
                continue;
            }

            $house = null;
            if ($row['house_code']) {
                $house = House::where('estate_id', $estate->id)
                    ->where('house_code', $row['house_code'])
                    ->first();
            }

            $resident = Resident::create([
                'estate_id' => $estate->id,
                'house_id' => $house?->id,
                'full_name' => $row['full_name'],
                'email' => $row['email'] ?: null,
                'phone' => $row['phone'] ?: null,
                'resident_type' => $row['resident_type'] ?: 'owner',
                'status' => $row['status'] ?: 'active',
            ]);

            if ($house) {
                $resident->attachToHouse($house);
            }

            $created++;
        }

        if ($created === 0) {
            throw ValidationException::withMessages(['file' => 'No residents were imported. Check headers, estate codes, or data.']);
        }

        return back()->with('status', "Imported {$created} resident(s).");
    }

    /**
     * @return array<int, array<string, string>>
     */
    protected function parseCsv(Request $request): array
    {
        $request->validate([
            'file' => ['required', 'file', 'mimes:csv,txt'],
        ]);

        $file = $request->file('file');
        $handle = fopen($file->getRealPath(), 'r');
        if (! $handle) {
            throw ValidationException::withMessages(['file' => 'Unable to read uploaded file.']);
        }

        $rows = [];
        $headers = null;

        while (($data = fgetcsv($handle, 0, ',')) !== false) {
            if (! $headers) {
                $headers = array_map(fn ($h) => Str::of($h)->lower()->trim()->toString(), $data);
                continue;
            }

            if (count($data) === 1 && trim($data[0]) === '') {
                continue;
            }

            $row = [];
            foreach ($headers as $index => $header) {
                $row[$header] = $data[$index] ?? null;
            }
            $rows[] = $row;
        }

        fclose($handle);

        if (! $headers) {
            throw ValidationException::withMessages(['file' => 'No header row found in CSV.']);
        }

        return $rows;
    }
}
