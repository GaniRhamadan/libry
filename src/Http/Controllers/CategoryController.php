<?php

declare(strict_types=1);

namespace RentalHub\StarterKit\Http\Controllers;

use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Routing\Controller;
use Illuminate\Support\Str;
use RentalHub\StarterKit\Http\Requests\CategoryRequest;
use RentalHub\StarterKit\Models\RentalCategory;

class CategoryController extends Controller
{
    public function index(): View
    {
        $categories = RentalCategory::withCount('units')
            ->orderBy('name')
            ->paginate(15);

        return view('rental-hub::categories.index', [
            'categories' => $categories,
        ]);
    }

    public function store(CategoryRequest $request): RedirectResponse
    {
        $name = (string) $request->input('name');
        $slug = $request->filled('slug')
            ? Str::slug((string) $request->input('slug'))
            : Str::slug($name);

        RentalCategory::create([
            'name' => $name,
            'slug' => $slug,
            'description' => $request->input('description'),
        ]);

        return redirect()->route('rental.categories.index')->with('success', (string) __('rental-hub::rental.category_created'));
    }

    public function update(CategoryRequest $request, RentalCategory $category): RedirectResponse
    {
        $name = (string) $request->input('name');
        $slug = $request->filled('slug')
            ? Str::slug((string) $request->input('slug'))
            : Str::slug($name);

        $category->update([
            'name' => $name,
            'slug' => $slug,
            'description' => $request->input('description'),
        ]);

        return redirect()->route('rental.categories.index')->with('success', (string) __('rental-hub::rental.category_updated'));
    }

    public function destroy(RentalCategory $category): RedirectResponse
    {
        if ($category->units()->exists()) {
            return back()->with('error', (string) __('rental-hub::rental.category_has_units'));
        }

        $category->delete();

        return redirect()->route('rental.categories.index')->with('success', (string) __('rental-hub::rental.category_deleted'));
    }
}
