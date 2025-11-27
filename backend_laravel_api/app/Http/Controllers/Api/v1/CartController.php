<?php

namespace App\Http\Controllers\Api\v1;

use App\Http\Controllers\Controller;
use App\Http\Requests\AddToCartRequest;
use App\Http\Requests\SyncCartRequest;
use App\Http\Resources\CartItemResource;
use App\Models\CartItem;
use App\Models\Product;
use Illuminate\Http\Request;

class CartController extends Controller
{
    /**
     * Get user's cart items
     */
    public function index(Request $request)
    {
        $user = $request->user();
        
        $cartItems = CartItem::with('product')
            ->forUser($user->id)
            ->get();
        
        $total = $cartItems->sum('total_price');
        
        return response()->json([
            'items' => CartItemResource::collection($cartItems),
            'total' => $total,
            'count' => $cartItems->sum('quantity'),
        ]);
    }

    /**
     * Add item to cart
     */
    public function add(AddToCartRequest $request)
    {
        $user = $request->user();
        $data = $request->validated();
        
        // Check if product exists and is active
        $product = Product::where('id', $data['product_id'])
            ->where('status', 'active')
            ->first();
        
        if (!$product) {
            return response()->json([
                'message' => 'Producto no disponible'
            ], 404);
        }
        
        // Check if item already exists in cart
        $cartItem = CartItem::forUser($user->id)
            ->where('product_id', $data['product_id'])
            ->first();
        
        if ($cartItem) {
            // Update quantity
            $cartItem->quantity += $data['quantity'];
            $cartItem->save();
        } else {
            // Create new cart item
            $cartItem = CartItem::create([
                'user_id' => $user->id,
                'product_id' => $data['product_id'],
                'quantity' => $data['quantity'],
            ]);
        }
        
        $cartItem->load('product');
        
        return response()->json([
            'message' => 'Producto añadido al carrito',
            'item' => new CartItemResource($cartItem),
        ], 201);
    }

    /**
     * Remove item from cart
     */
    public function remove(Request $request, $id)
    {
        $user = $request->user();
        
        $cartItem = CartItem::forUser($user->id)
            ->where('id', $id)
            ->first();
        
        if (!$cartItem) {
            return response()->json([
                'message' => 'Item no encontrado en el carrito'
            ], 404);
        }
        
        $cartItem->delete();
        
        return response()->json([
            'message' => 'Producto eliminado del carrito'
        ]);
    }

    /**
     * Clear entire cart
     */
    public function clear(Request $request)
    {
        $user = $request->user();
        
        CartItem::forUser($user->id)->delete();
        
        return response()->json([
            'message' => 'Carrito vaciado'
        ]);
    }

    /**
     * Sync cart from localStorage after login
     */
    public function sync(SyncCartRequest $request)
    {
        $user = $request->user();
        $data = $request->validated();
        
        $syncedCount = 0;
        
        foreach ($data['items'] as $item) {
            // Check if product exists and is active
            $product = Product::where('id', $item['product_id'])
                ->where('status', 'active')
                ->first();
            
            if (!$product) {
                continue; // Skip invalid products
            }
            
            // Check if item already exists in cart
            $cartItem = CartItem::forUser($user->id)
                ->where('product_id', $item['product_id'])
                ->first();
            
            if ($cartItem) {
                // Update quantity (add to existing)
                $cartItem->quantity += $item['quantity'];
                $cartItem->save();
            } else {
                // Create new cart item
                CartItem::create([
                    'user_id' => $user->id,
                    'product_id' => $item['product_id'],
                    'quantity' => $item['quantity'],
                ]);
            }
            
            $syncedCount++;
        }
        
        return response()->json([
            'message' => 'Carrito sincronizado',
            'synced_items' => $syncedCount,
        ]);
    }
}
