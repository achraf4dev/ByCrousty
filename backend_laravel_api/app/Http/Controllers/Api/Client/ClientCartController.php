<?php

namespace App\Http\Controllers\Api\Client;

use App\Http\Controllers\Controller;
use App\Http\Requests\AddToCartRequest;
use App\Http\Requests\SyncCartRequest;
use App\Http\Resources\CartItemResource;
use App\Models\CartItem;
use Illuminate\Http\Request;

class ClientCartController extends Controller
{
    /**
     * Get user's cart
     */
    public function index(Request $request)
    {
        $cartItems = CartItem::with('product')
            ->where('user_id', $request->user()->id)
            ->get();

        return response()->json([
            'items' => CartItemResource::collection($cartItems),
            'total' => $cartItems->sum('total_price'),
            'count' => $cartItems->sum('quantity'),
        ]);
    }

    /**
     * Add item to cart
     */
    public function add(AddToCartRequest $request)
    {
        $userId = $request->user()->id;
        $productId = $request->product_id;
        $quantity = $request->quantity;

        // Check if item already exists in cart
        $cartItem = CartItem::where('user_id', $userId)
            ->where('product_id', $productId)
            ->first();

        if ($cartItem) {
            // Update quantity if item exists
            $cartItem->quantity += $quantity;
            $cartItem->save();
        } else {
            // Create new cart item
            $cartItem = CartItem::create([
                'user_id' => $userId,
                'product_id' => $productId,
                'quantity' => $quantity,
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
        $cartItem = CartItem::where('user_id', $request->user()->id)
            ->where('id', $id)
            ->firstOrFail();

        $cartItem->delete();

        return response()->json([
            'message' => 'Producto eliminado del carrito',
        ]);
    }

    /**
     * Clear entire cart
     */
    public function clear(Request $request)
    {
        CartItem::where('user_id', $request->user()->id)->delete();

        return response()->json([
            'message' => 'Carrito vaciado',
        ]);
    }

    /**
     * Sync cart from localStorage after login
     */
    public function sync(SyncCartRequest $request)
    {
        $userId = $request->user()->id;
        $items = $request->items;

        foreach ($items as $item) {
            $productId = $item['product_id'];
            $quantity = $item['quantity'];

            // Check if item already exists in cart
            $cartItem = CartItem::where('user_id', $userId)
                ->where('product_id', $productId)
                ->first();

            if ($cartItem) {
                // Update quantity if item exists
                $cartItem->quantity += $quantity;
                $cartItem->save();
            } else {
                // Create new cart item
                CartItem::create([
                    'user_id' => $userId,
                    'product_id' => $productId,
                    'quantity' => $quantity,
                ]);
            }
        }

        $cartItems = CartItem::with('product')
            ->where('user_id', $userId)
            ->get();

        return response()->json([
            'message' => 'Carrito sincronizado',
            'items' => CartItemResource::collection($cartItems),
            'count' => $cartItems->sum('quantity'),
        ]);
    }
}
