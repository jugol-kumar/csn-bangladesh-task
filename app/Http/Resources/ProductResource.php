<?php

namespace App\Http\Resources;

use App\Services\ImageService;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProductResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $attributes = $this->resource->getAttributes();

        return [
            'id' => $this->id,
            'name' => $this->name,
            'description' => $this->description,
            'base_price' => (float) $this->base_price,
            'thumbnail_url' => ImageService::url($this->thumbnail),
            'source' => $this->source,
            'creator' => $this->whenLoaded('creator', fn () => $this->creator ? [
                'id' => $this->creator->id,
                'name' => $this->creator->name,
            ] : null),
            'listings_count' => $this->whenCounted('listings'),
            'is_listed' => $this->when(
                array_key_exists('is_listed', $attributes),
                fn () => (bool) $this->resource->getAttribute('is_listed')
            ),
            'is_mine' => $this->when(
                array_key_exists('is_mine', $attributes),
                fn () => (bool) $this->resource->getAttribute('is_mine')
            ),
            'created_at' => $this->created_at?->toDateTimeString(),
        ];
    }
}
