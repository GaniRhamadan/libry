<?php

declare(strict_types=1);

namespace RentalHub\StarterKit\Http\Controllers;

use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Storage;
use RentalHub\StarterKit\Enums\UnitStatus;
use RentalHub\StarterKit\Http\Requests\UnitRequest;
use RentalHub\StarterKit\Models\RentalCategory;
use RentalHub\StarterKit\Models\RentalUnit;

class UnitController extends Controller
{
    public function index(Request $request): View
    {
        $filters = [
            'search' => $request->query('search'),
            'category_id' => $request->query('category_id'),
            'status' => $request->query('status'),
        ];

        $units = RentalUnit::with('category')
            ->filter($filters)
            ->latest()
            ->paginate(12)
            ->withQueryString();

        $categories = RentalCategory::orderBy('name')->get();

        return view('rental-hub::units.index', [
            'units' => $units,
            'categories' => $categories,
            'filters' => $filters,
            'statuses' => UnitStatus::cases(),
        ]);
    }

    public function create(): View
    {
        $categories = RentalCategory::orderBy('name')->get();

        return view('rental-hub::units.form', [
            'unit' => new RentalUnit(),
            'categories' => $categories,
            'statuses' => UnitStatus::cases(),
            'isEdit' => false,
        ]);
    }

    public function store(UnitRequest $request): RedirectResponse
    {
        $disk = (string) config('rental-hub.disk', 'public');
        $photoPath = null;

        if ($request->hasFile('photo')) {
            $photoPath = $request->file('photo')->store('rental-units', $disk);
        }

        $specifications = $this->parseSpecifications(
            (array) $request->input('spec_keys', []),
            (array) $request->input('spec_values', [])
        );

        RentalUnit::create([
            'category_id' => (int) $request->input('category_id'),
            'name' => (string) $request->input('name'),
            'code' => strtoupper((string) $request->input('code')),
            'price_per_hour' => (int) $request->input('price_per_hour'),
            'price_per_day' => (int) $request->input('price_per_day'),
            'status' => (string) $request->input('status'),
            'late_fee_per_hour' => $request->filled('late_fee_per_hour') ? (int) $request->input('late_fee_per_hour') : null,
            'specifications' => $specifications,
            'photo_path' => $photoPath,
        ]);

        return redirect()->route('rental.units.index')->with('success', (string) __('rental-hub::rental.unit_created'));
    }

    public function edit(RentalUnit $unit): View
    {
        $categories = RentalCategory::orderBy('name')->get();

        return view('rental-hub::units.form', [
            'unit' => $unit,
            'categories' => $categories,
            'statuses' => UnitStatus::cases(),
            'isEdit' => true,
        ]);
    }

    public function update(UnitRequest $request, RentalUnit $unit): RedirectResponse
    {
        $disk = (string) config('rental-hub.disk', 'public');
        $photoPath = $unit->photo_path;

        if ($request->hasFile('photo')) {
            if ($photoPath !== null && Storage::disk($disk)->exists($photoPath)) {
                Storage::disk($disk)->delete($photoPath);
            }
            $photoPath = $request->file('photo')->store('rental-units', $disk);
        }

        $specifications = $this->parseSpecifications(
            (array) $request->input('spec_keys', []),
            (array) $request->input('spec_values', [])
        );

        $unit->update([
            'category_id' => (int) $request->input('category_id'),
            'name' => (string) $request->input('name'),
            'code' => strtoupper((string) $request->input('code')),
            'price_per_hour' => (int) $request->input('price_per_hour'),
            'price_per_day' => (int) $request->input('price_per_day'),
            'status' => (string) $request->input('status'),
            'late_fee_per_hour' => $request->filled('late_fee_per_hour') ? (int) $request->input('late_fee_per_hour') : null,
            'specifications' => $specifications,
            'photo_path' => $photoPath,
        ]);

        return redirect()->route('rental.units.index')->with('success', (string) __('rental-hub::rental.unit_updated'));
    }

    public function destroy(RentalUnit $unit): RedirectResponse
    {
        $unit->delete();

        return redirect()->route('rental.units.index')->with('success', (string) __('rental-hub::rental.unit_deleted'));
    }

    /**
     * @param array<int, mixed> $keys
     * @param array<int, mixed> $values
     * @return array<string, string>
     */
    private function parseSpecifications(array $keys, array $values): array
    {
        $specs = [];
        foreach ($keys as $index => $key) {
            $trimmedKey = trim((string) $key);
            $trimmedVal = trim((string) ($values[$index] ?? ''));

            if ($trimmedKey !== '' && $trimmedVal !== '') {
                $specs[$trimmedKey] = $trimmedVal;
            }
        }

        return $specs;
    }
}
