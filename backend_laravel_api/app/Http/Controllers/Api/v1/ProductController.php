<?php

    namespace App\Http\Controllers\Api\v1;

    use App\Http\Controllers\Controller;
    use App\Models\Product;
    use App\Models\Category;
    use Illuminate\Http\Request;
    use Illuminate\Http\JsonResponse;

    class ProductController extends Controller
    {
        /**
         * Get most sold active products based on orders
         */
        public function mostSold(Request $request): JsonResponse
        {
            // Only count completed orders
            $orders = \App\Models\Order::where('status', 'completed')->get(['items']);
            $productCounts = [];
            foreach ($orders as $order) {
                $items = is_array($order->items) ? $order->items : json_decode($order->items, true);
                if (is_array($items)) {
                    foreach ($items as $item) {
                        $pid = $item['product_id'] ?? null;
                        $qty = $item['quantity'] ?? 1;
                        if ($pid) {
                            if (!isset($productCounts[$pid])) $productCounts[$pid] = 0;
                            $productCounts[$pid] += $qty;
                        }
                    }
                }
            }
            if (empty($productCounts)) {
                // No sales yet, return 8 random active products
                $products = Product::where('status', 'active')->with('category')->inRandomOrder()->limit(9)->get()->values();
            } else {
                // Get active products only, sorted by sold_count
                $products = Product::where('status', 'active')
                    ->whereIn('id', array_keys($productCounts))
                    ->with('category')
                    ->get()
                    ->map(function($product) use ($productCounts) {
                        $product->sold_count = $productCounts[$product->id] ?? 0;
                        return $product;
                    })
                    ->sortByDesc('sold_count')
                    ->values()
                    ->take(9)
                    ->values();
            }
            return response()->json([
                'success' => true,
                'message' => 'Most sold products retrieved successfully',
                'total_sold_products' => count($products),
                'data' => $products
            ]);
        }
    /**
     * Get all products
     */
    public function index(Request $request): JsonResponse
    {
        try {
            $query = Product::with('category');

            // Filter by status
            if ($request->has('status')) {
                $query->where('status', $request->status);
            }

            // Filter by category
            if ($request->has('category_id')) {
                $query->where('category_id', $request->category_id);
            }

            // Search by name or description
            if ($request->has('search')) {
                $search = $request->search;
                $query->where(function ($q) use ($search) {
                    $q->where('name', 'like', '%' . $search . '%')
                      ->orWhere('description', 'like', '%' . $search . '%');
                });
            }

            // Filter by price range
            if ($request->has('min_price')) {
                $query->where('price', '>=', $request->min_price);
            }
            if ($request->has('max_price')) {
                $query->where('price', '<=', $request->max_price);
            }

            // Filter by points range
            if ($request->has('min_points')) {
                $query->where('points', '>=', $request->min_points);
            }
            if ($request->has('max_points')) {
                $query->where('points', '<=', $request->max_points);
            }

            // Sort options
            $sortBy = $request->get('sort_by', 'created_at');
            $sortOrder = $request->get('sort_order', 'desc');
            
            if (in_array($sortBy, ['name', 'price', 'points', 'created_at'])) {
                $query->orderBy($sortBy, $sortOrder);
            }

            $products = $query->paginate($request->get('per_page', 15));

            return response()->json([
                'success' => true,
                'message' => 'Products retrieved successfully',
                'data' => $products
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve products',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get a specific product
     */
    public function show($id): JsonResponse
    {
        try {
            $product = Product::with('category')->findOrFail($id);

            return response()->json([
                'success' => true,
                'message' => 'Product retrieved successfully',
                'data' => $product
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Product not found',
                'error' => $e->getMessage()
            ], 404);
        }
    }

    /**
     * Get active products only
     */
    public function active(Request $request): JsonResponse
    {
        try {
            $query = Product::with('category')->where('status', 'active');

            // Filter by category
            if ($request->has('category_id')) {
                $query->where('category_id', $request->category_id);
            }

            // Search
            if ($request->has('search')) {
                $search = $request->search;
                $query->where(function ($q) use ($search) {
                    $q->where('name', 'like', '%' . $search . '%')
                      ->orWhere('description', 'like', '%' . $search . '%');
                });
            }

            // Sort
            $sortBy = $request->get('sort_by', 'created_at');
            $sortOrder = $request->get('sort_order', 'desc');
            
            if (in_array($sortBy, ['name', 'price', 'points', 'created_at'])) {
                $query->orderBy($sortBy, $sortOrder);
            }

            $products = $query->paginate($request->get('per_page', 15));

            return response()->json([
                'success' => true,
                'message' => 'Active products retrieved successfully',
                'data' => $products
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve active products',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get products by category
     */
    public function byCategory($categoryId, Request $request): JsonResponse
    {
        try {
            // Verify category exists
            $category = Category::findOrFail($categoryId);

            $query = Product::with('category')
                ->where('category_id', $categoryId)
                ->where('status', 'active');

            // Search
            if ($request->has('search')) {
                $search = $request->search;
                $query->where(function ($q) use ($search) {
                    $q->where('name', 'like', '%' . $search . '%')
                      ->orWhere('description', 'like', '%' . $search . '%');
                });
            }

            // Sort
            $sortBy = $request->get('sort_by', 'created_at');
            $sortOrder = $request->get('sort_order', 'desc');
            
            if (in_array($sortBy, ['name', 'price', 'points', 'created_at'])) {
                $query->orderBy($sortBy, $sortOrder);
            }

            $products = $query->paginate($request->get('per_page', 15));

            return response()->json([
                'success' => true,
                'message' => 'Products retrieved successfully',
                'data' => [
                    'category' => $category,
                    'products' => $products
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve products by category',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get products with points (promotional products)
     */
    public function withPoints(Request $request): JsonResponse
    {
        try {
            $query = Product::with('category')
                ->where('status', 'active')
                ->where('points', '>', 0)
                ->orderBy('points', 'asc'); // Sort by points ascending (lowest first)

            $products = $query->paginate($request->get('per_page', 15));

            return response()->json([
                'success' => true,
                'message' => 'Promotional products retrieved successfully',
                'data' => $products
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve promotional products',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}