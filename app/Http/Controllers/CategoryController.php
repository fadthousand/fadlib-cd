<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class CategoryController extends Controller
{
    public function index(Request $request): View
    {
        $search = $request->string('search')->trim()->toString();

        $categories = Category::query()
            ->withCount('books')
            ->withSum('books', 'stock')
            ->when($search !== '', function ($query) use ($search): void {
                $query->where(function ($subQuery) use ($search): void {
                    $subQuery->where('name', 'like', "%{$search}%")
                        ->orWhere('author', 'like', "%{$search}%");
                });
            })
            ->orderBy('name')
            ->get();

        return view('categories.index', compact('categories', 'search'));
    }

    public function create(): View
    {
        return view('categories.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255', 'unique:categories,name'],
            'author' => ['required', 'string', 'max:255'],
            'epigraph' => ['nullable', 'string', 'max:500'],
            'description' => ['nullable', 'string'],
        ]);

        $baseSlug = Str::slug($validated['name']);
        $slug = $baseSlug;
        $counter = 2;

        while (Category::query()->where('slug', $slug)->exists()) {
            $slug = "{$baseSlug}-{$counter}";
            $counter++;
        }

        Category::query()->create([
            'name' => $validated['name'],
            'slug' => $slug,
            'author' => $validated['author'],
            'epigraph' => $validated['epigraph'] ?? null,
            'description' => $validated['description'] ?? null,
        ]);

        return redirect()
            ->route('categories.index')
            ->with('status', 'Series category added to the archive.');
    }

    public function edit(Category $category): View
    {
        return view('categories.edit', compact('category'));
    }

    public function update(Request $request, Category $category): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255', Rule::unique('categories', 'name')->ignore($category->id)],
            'author' => ['required', 'string', 'max:255'],
            'epigraph' => ['nullable', 'string', 'max:500'],
            'description' => ['nullable', 'string'],
        ]);

        $baseSlug = Str::slug($validated['name']);
        $slug = $baseSlug;
        $counter = 2;

        while (Category::query()->where('slug', $slug)->where('id', '!=', $category->id)->exists()) {
            $slug = "{$baseSlug}-{$counter}";
            $counter++;
        }

        $category->update([
            'name' => $validated['name'],
            'slug' => $slug,
            'author' => $validated['author'],
            'epigraph' => $validated['epigraph'] ?? null,
            'description' => $validated['description'] ?? null,
        ]);

        return redirect()
            ->route('categories.index')
            ->with('status', 'Series category updated.');
    }

    public function destroy(Category $category): RedirectResponse
    {
        if ($category->books()->exists()) {
            return back()->with('error', 'Cannot delete a series that still contains archived volumes.');
        }

        $category->delete();

        return redirect()
            ->route('categories.index')
            ->with('status', 'Series category deleted.');
    }
}
