<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ProductController extends Controller
{
    /**
     * Display a listing of products
     */
    public function index(Request $request)
    {
        $query = Product::with('category');

        // Filter by category
        if ($request->has('category_id') && $request->category_id) {
            $query->where('category_id', $request->category_id);
        }

        // Filter by status
        if ($request->has('status') && $request->status) {
            $query->where('status', $request->status);
        }

        // Search
        if ($request->has('search') && $request->search) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', '%' . $search . '%')
                  ->orWhere('description', 'like', '%' . $search . '%');
            });
        }

        $products = $query->latest()->paginate(15);
        $categories = Category::active()->get();

        return view('admin.products.index', compact('products', 'categories'));
    }

    /**
     * Show the form for creating a new product
     */
    public function create()
    {
        $categories = Category::active()->get();
        return view('admin.products.create', compact('categories'));
    }

    /**
     * Store a newly created product
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'price' => 'nullable|numeric|min:0',
            'points' => 'required|integer|min:0',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
            'category_id' => 'required|exists:categories,id',
            'status' => 'nullable|string'
        ]);

        $data = [
            'name' => $request->name,
            'description' => $request->description,
            'price' => $request->price ?? 0,
            'points' => $request->points,
            'category_id' => $request->category_id,
            'status' => $request->has('status') ? 'active' : 'inactive'
        ];

        // Handle image upload
        if ($request->hasFile('image')) {
            $file = $request->file('image');
            
            if ($file->isValid()) {
                try {
                    $data['image'] = $file->store('products', 'public');
                } catch (\Exception $e) {
                    return redirect()->back()
                        ->withInput()
                        ->with('error', 'Error uploading image: ' . $e->getMessage());
                }
            } else {
                return redirect()->back()
                    ->withInput()
                    ->with('error', 'Invalid image file uploaded.');
            }
        }

        try {
            Product::create($data);
            
            return redirect()->route('admin.products.index')
                ->with('success', 'Product created successfully.');
        } catch (\Exception $e) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Error creating product: ' . $e->getMessage());
        }
    }

    /**
     * Display the specified product
     */
    public function show(Product $product)
    {
        $product->load('category');
        
        // Get related products from the same category, excluding current product
        $relatedProducts = Product::where('category_id', $product->category_id)
            ->where('id', '!=', $product->id)
            ->where('status', 'active')
            ->limit(4)
            ->get();
        
        return view('admin.products.show', compact('product', 'relatedProducts'));
    }

    /**
     * Show the form for editing the product
     */
    public function edit(Product $product)
    {
        $categories = Category::active()->get();
        return view('admin.products.edit', compact('product', 'categories'));
    }

    /**
     * Update the specified product
     */
    public function update(Request $request, Product $product)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'price' => 'nullable|numeric|min:0',
            'points' => 'required|integer|min:0',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
            'category_id' => 'required|exists:categories,id',
            'status' => 'nullable|string'
        ]);

        $data = [
            'name' => $request->name,
            'description' => $request->description,
            'price' => $request->price ?? 0,
            'points' => $request->points,
            'category_id' => $request->category_id,
            'status' => $request->has('status') ? 'active' : 'inactive'
        ];

        // Handle image upload
        if ($request->hasFile('image')) {
            // Delete old image
            if ($product->image && Storage::disk('public')->exists($product->image)) {
                Storage::disk('public')->delete($product->image);
            }
            $data['image'] = $request->file('image')->store('products', 'public');
        }

        try {
            $product->update($data);
            
            return redirect()->route('admin.products.index')
                ->with('success', 'Product updated successfully.');
        } catch (\Exception $e) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Error updating product: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified product
     */
    public function destroy(Product $product)
    {
        // Delete image
        if ($product->image && Storage::disk('public')->exists($product->image)) {
            Storage::disk('public')->delete($product->image);
        }

        $product->delete();

        return redirect()->route('admin.products.index')
            ->with('success', 'Product deleted successfully.');
    }

    /**
     * Toggle product status
     */
    public function toggleStatus(Product $product)
    {
        $product->update([
            'status' => $product->status === 'active' ? 'inactive' : 'active'
        ]);

        // Return JSON if request expects JSON (AJAX)
        if (request()->expectsJson()) {
            return response()->json([
                'success' => true,
                'status' => $product->fresh()->status
            ]);
        }

        return redirect()->back()
            ->with('success', 'Product status updated successfully.');
    }

    /**
     * Bulk delete products
     */
    public function bulkDelete(Request $request)
    {
        $request->validate([
            'product_ids' => 'required|array',
            'product_ids.*' => 'exists:products,id'
        ]);

        $products = Product::whereIn('id', $request->product_ids)->get();

        foreach ($products as $product) {
            // Delete image
            if ($product->image && Storage::disk('public')->exists($product->image)) {
                Storage::disk('public')->delete($product->image);
            }
        }

        Product::whereIn('id', $request->product_ids)->delete();

        return redirect()->route('admin.products.index')
            ->with('success', 'Selected products deleted successfully.');
    }
}