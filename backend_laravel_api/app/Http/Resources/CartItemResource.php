<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CartItemResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'user_id' => $this->user_id,
            'product_id' => $this->product_id,
            'quantity' => $this->quantity,
            'product' => new ProductResource($this->whenLoaded('product')),
            'name' => $this->product->name ?? null,
            'price' => $this->product ? (float) $this->product->price : 0,
            'image' => $this->product->image ?? null,
            'image_url' => $this->product->image_url ?? null,
            'total_price' => $this->total_price ?? 0,
            'created_at' => $this->created_at?->toISOString(),
            'updated_at' => $this->updated_at?->toISOString(),
        ];
    }
}
