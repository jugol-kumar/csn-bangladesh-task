<?php

namespace App\Http\Resources;

use App\Services\ImageService;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ListingResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $product = $this->resource->product;

        return [
            'id' => $this->id,
            'shop_id' => $this->shop_id,
            'product_id' => $this->product_id,
            'title' => $this->title ?? $product?->name,
            'description' => $this->description ?? $product?->description,
            'price' => (float) $this->price,
            'thumbnail_url' => ImageService::url($this->thumbnail ?? $product?->thumbnail),
            'own_thumbnail_url' => ImageService::url($this->thumbnail),
            'is_active' => (bool) $this->is_active,
            'product' => $this->whenLoaded('product', fn () => [
                'id' => $this->product->id,
                'name' => $this->product->name,
                'base_price' => (float) $this->product->base_price,
            ]),
            'shop' => $this->whenLoaded('shop', fn () => [
                'id' => $this->shop->id,
                'name' => $this->shop->name,
                'slug' => $this->shop->slug,
            ]),
            'created_at' => $this->created_at?->toDateTimeString(),
        ];
    }
}
