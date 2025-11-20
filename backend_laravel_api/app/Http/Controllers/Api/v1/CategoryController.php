<?php

namespace App\Http\Controllers\Api\v1;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

    class CategoryController extends Controller
    {
        /**
         * Get most sold active categories based on orders
         */
        public function mostSold(Request $request): JsonResponse
        {
            $orders = \App\Models\Order::where('status', 'completed')->get(['items']);
            $categoryCounts = [];
            foreach ($orders as $order) {
                $items = is_array($order->items) ? $order->items : json_decode($order->items, true);
                if (is_array($items)) {
                    foreach ($items as $item) {
                        $pid = $item['product_id'] ?? null;
                        $qty = $item['quantity'] ?? 1;
                        if ($pid) {
                            $product = \App\Models\Product::find($pid);
                            if ($product && $product->status === 'active' && $product->category_id) {
                                $cid = $product->category_id;
                                if (!isset($categoryCounts[$cid])) $categoryCounts[$cid] = 0;
                                $categoryCounts[$cid] += $qty;
                            }
                        }
                    }
                }
            }
            if (empty($categoryCounts)) {
                // No sales yet, return 4 random active categories
                $categories = Category::where('status', 'active')->inRandomOrder()->limit(4)->get()->values();
            } else {
                // Get active categories only, sorted by sold_count
                $categories = Category::where('status', 'active')
                    ->whereIn('id', array_keys($categoryCounts))
                    ->get()
                    ->map(function($category) use ($categoryCounts) {
                        $category->sold_count = $categoryCounts[$category->id] ?? 0;
                        return $category;
                    })
                    ->sortByDesc('sold_count')
                    ->values()
                    ->take(4)
                    ->values();
            }
            return response()->json([
                'success' => true,
                'message' => 'Most sold categories retrieved successfully',
                'data' => $categories
            ]);
        }
    /**
     * Get all categories
     */
    public function index(Request $request): JsonResponse
    {
        try {
            $query = Category::query();

            // Filter by status
            if ($request->has('status')) {
                $query->where('status', $request->status);
            }

            // Search by name
            if ($request->has('search')) {
                $query->where('name', 'like', '%' . $request->search . '%');
            }

            // Include products count
            if ($request->has('include_products_count')) {
                $query->withCount('products');
            }

            // Include products
            if ($request->has('include_products')) {
                $query->with(['products' => function ($q) {
                    $q->where('status', 'active');
                }]);
            }

            $categories = $query->latest()->paginate($request->get('per_page', 15));

            return response()->json([
                'success' => true,
                'message' => 'Categories retrieved successfully',
                'data' => $categories
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve categories',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get a specific category
     */
    public function show($id, Request $request): JsonResponse
    {
        try {
            $query = Category::query();

            // Include products if requested
            if ($request->has('include_products')) {
                $query->with(['products' => function ($q) {
                    $q->where('status', 'active')->latest();
                }]);
            }

            $category = $query->findOrFail($id);

            return response()->json([
                'success' => true,
                'message' => 'Category retrieved successfully',
                'data' => $category
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Category not found',
                'error' => $e->getMessage()
            ], 404);
        }
    }

    /**
     * Get active categories only
     */
    public function active(): JsonResponse
    {
        try {
            $categories = Category::active()
                ->withCount('activeProducts')
                ->latest()
                ->get();

            return response()->json([
                'success' => true,
                'message' => 'Active categories retrieved successfully',
                'data' => $categories
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve active categories',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}