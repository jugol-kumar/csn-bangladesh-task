<?php

namespace App\Services;

use App\Models\Shop;
use App\Models\ShopProduct;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Pagination\LengthAwarePaginator;

class StorefrontService
{
    public function shops(?string $search, int $perPage = 12): LengthAwarePaginator
    {
        return Shop::query()
            ->where('is_active', true)
            ->whereHas('listings', fn (Builder $query) => $query->where('is_active', true))
            ->withCount(['listings' => fn (Builder $query) => $query->where('is_active', true)])
            ->when($search, fn (Builder $query) => $query->where('name', 'like', "%{$search}%"))
            ->orderBy('name')
            ->paginate($perPage);
    }

    public function shop(Shop $shop): Shop
    {
        return $shop->loadCount(['listings' => fn (Builder $query) => $query->where('is_active', true)]);
    }

    public function products(Shop $shop, ?string $search, ?string $sort, int $perPage = 12): LengthAwarePaginator
    {
        return $this->listings($shop->id, $search, $sort, $perPage);
    }

    public function latest(?string $search, ?string $sort, int $perPage = 12): LengthAwarePaginator
    {
        return $this->listings(null, $search, $sort, $perPage);
    }

    public function listing(Shop $shop, ShopProduct $listing): ?ShopProduct
    {
        $visible = ShopProduct::query()
            ->visible()
            ->where('shop_id', $shop->id)
            ->whereKey($listing->id)
            ->with(['product', 'shop:id,name,slug,address,phone'])
            ->first();

        return $visible;
    }

    private function listings(?int $shopId, ?string $search, ?string $sort, int $perPage): LengthAwarePaginator
    {
        return ShopProduct::query()
            ->visible()
            ->with(['product', 'shop:id,name,slug'])
            ->when($shopId, fn (Builder $query) => $query->where('shop_id', $shopId))
            ->when($search, function (Builder $query) use ($search) {
                $query->where(function (Builder $inner) use ($search) {
                    $inner->where('title', 'like', "%{$search}%")
                        ->orWhereHas('product', fn (Builder $product) => $product->where('name', 'like', "%{$search}%"));
                });
            })
            ->when($sort === 'price_asc', fn (Builder $query) => $query->orderBy('price'))
            ->when($sort === 'price_desc', fn (Builder $query) => $query->orderByDesc('price'))
            ->orderByDesc('id')
            ->paginate($perPage);
    }
}
