<?php
namespace App\Http\Controllers\Api\v1;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\Request;

class MenuController extends Controller
{
    /**
     * Get all active categories with their products
     */
    public function index()
    {
        $categories = Category::where('status', 1)
            ->with(['products' => function($query) {
                $query->where('status', 1);
            }])
            ->get();

        return response()->json([
            'data' => $categories
        ]);
    }
}
