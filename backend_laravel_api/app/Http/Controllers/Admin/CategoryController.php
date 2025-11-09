<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class CategoryController extends Controller
{
    /**
     * Display a listing of categories
     */
    public function index()
    {
        $categories = Category::withCount('products')
            ->latest()
            ->paginate(15);

        return view('admin.categories.index', compact('categories'));
    }

    /**
     * Show the form for creating a new category
     */
    public function create()
    {
        return view('admin.categories.create');
    }

    /**
     * Store a newly created category
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:categories,name',
            'description' => 'nullable|string',
            'slug' => 'nullable|string|unique:categories,slug',
            'status' => 'nullable|string'
        ]);

        $data = [
            'name' => $request->name,
            'description' => $request->description,
            'status' => $request->has('status') ? 'active' : 'inactive'
        ];

        // Generate slug if not provided
        if ($request->filled('slug')) {
            $data['slug'] = Str::slug($request->slug);
        } else {
            $data['slug'] = Str::slug($request->name);
        }

        // Ensure slug is unique
        $originalSlug = $data['slug'];
        $counter = 1;
        while (Category::where('slug', $data['slug'])->exists()) {
            $data['slug'] = $originalSlug . '-' . $counter;
            $counter++;
        }

        try {
            Category::create($data);
            
            return redirect()->route('admin.categories.index')
                ->with('success', 'Category created successfully.');
        } catch (\Exception $e) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Error creating category: ' . $e->getMessage());
        }
    }

    /**
     * Display the specified category
     */
    public function show(Category $category)
    {
        $category->load(['products' => function ($query) {
            $query->latest()->paginate(10);
        }]);

        return view('admin.categories.show', compact('category'));
    }

    /**
     * Show the form for editing the category
     */
    public function edit(Category $category)
    {
        return view('admin.categories.edit', compact('category'));
    }

    /**
     * Update the specified category
     */
    public function update(Request $request, Category $category)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:categories,name,' . $category->id,
            'description' => 'nullable|string',
            'slug' => 'nullable|string|unique:categories,slug,' . $category->id,
            'status' => 'nullable|string'
        ]);

        $data = [
            'name' => $request->name,
            'description' => $request->description,
            'status' => $request->has('status') ? 'active' : 'inactive'
        ];

        // Generate slug if name changed and no slug provided
        if ($request->name !== $category->name) {
            if ($request->filled('slug')) {
                $data['slug'] = Str::slug($request->slug);
            } else {
                $data['slug'] = Str::slug($request->name);
            }
            
            // Ensure slug is unique (but allow current category's slug)
            $originalSlug = $data['slug'];
            $counter = 1;
            while (Category::where('slug', $data['slug'])->where('id', '!=', $category->id)->exists()) {
                $data['slug'] = $originalSlug . '-' . $counter;
                $counter++;
            }
        }

        try {
            $category->update($data);
            
            return redirect()->route('admin.categories.index')
                ->with('success', 'Category updated successfully.');
        } catch (\Exception $e) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Error updating category: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified category
     */
    public function destroy(Category $category)
    {
        // Check if category has products
        if ($category->products()->count() > 0) {
            return redirect()->route('admin.categories.index')
                ->with('error', 'Cannot delete category with existing products.');
        }

        $category->delete();

        return redirect()->route('admin.categories.index')
            ->with('success', 'Category deleted successfully.');
    }

    /**
     * Toggle category status
     */
    public function toggleStatus(Category $category)
    {
        $newStatus = $category->status === 'active' ? 'inactive' : 'active';
        
        $category->update(['status' => $newStatus]);

        // If category becomes inactive, make all its products inactive too
        if ($newStatus === 'inactive') {
            $category->products()->where('status', 'active')->update(['status' => 'inactive']);
        }

        // Return JSON if request expects JSON (AJAX)
        if (request()->expectsJson()) {
            $affectedProducts = $newStatus === 'inactive' ? $category->products()->count() : 0;
            
            return response()->json([
                'success' => true,
                'status' => $category->fresh()->status,
                'message' => $newStatus === 'inactive' 
                    ? "Category deactivated. {$affectedProducts} products were also deactivated."
                    : 'Category activated successfully.',
                'affected_products' => $affectedProducts
            ]);
        }

        $message = $newStatus === 'inactive' 
            ? 'Category deactivated. All products in this category were also deactivated.'
            : 'Category status updated successfully.';

        return redirect()->back()->with('success', $message);
    }
}