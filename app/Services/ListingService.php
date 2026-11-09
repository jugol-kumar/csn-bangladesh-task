<?php

namespace App\Services;

use App\Models\Product;
use App\Models\Shop;
use App\Models\ShopProduct;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Pagination\LengthAwarePaginator;

class ListingService
{
    public function __construct(private ImageService $images)
    {
    }

    public function forShop(Shop $shop, ?string $search, int $perPage = 12): LengthAwarePaginator
    {
        return ShopProduct::query()
            ->where('shop_id', $shop->id)
            ->with(['product', 'shop:id,name,slug'])
            ->when($search, function (Builder $query) use ($search) {
                $query->where(function (Builder $inner) use ($search) {
                    $inner->where('title', 'like', "%{$search}%")
                        ->orWhereHas('product', fn (Builder $product) => $product->where('name', 'like', "%{$search}%"));
                });
            })
            ->latest('id')
            ->paginate($perPage);
    }

    public function catalog(Shop $shop, int $userId, ?string $search, string $scope, int $perPage = 12): LengthAwarePaginator
    {
        $page = Product::query()
            ->with('creator:id,name')
            ->withExists(['listings as is_listed' => fn (Builder $query) => $query->where('shop_id', $shop->id)])
            ->when($search, fn (Builder $query) => $query->where('name', 'like', "%{$search}%"))
            ->when($scope === 'available', fn (Builder $query) => $query->whereDoesntHave(
                'listings',
                fn (Builder $listing) => $listing->where('shop_id', $shop->id)
            ))
            ->when($scope === 'mine', fn (Builder $query) => $query->where('created_by', $userId))
            ->latest('id')
            ->paginate($perPage);

        $page->getCollection()->each(
            fn (Product $product) => $product->setAttribute('is_mine', $product->created_by === $userId)
        );

        return $page;
    }

    public function attach(Shop $shop, Product $product, array $data): ShopProduct
    {
        $overrides = $this->overrides($product, $data);

        $listing = ShopProduct::create([
            'shop_id' => $shop->id,
            'product_id' => $product->id,
            'title' => $overrides['title'],
            'description' => $overrides['description'],
            'price' => $data['price'] ?? $product->base_price,
            'thumbnail' => isset($data['thumbnail'])
                ? $this->images->store($data['thumbnail'], 'listings')
                : null,
            'is_active' => $data['is_active'] ?? true,
        ]);

        return $listing->load(['product', 'shop:id,name,slug']);
    }

    public function update(ShopProduct $listing, array $data): ShopProduct
    {
        $listing->loadMissing('product');
        $overrides = $this->overrides($listing->product, $data);

        $listing->update([
            'title' => $overrides['title'],
            'description' => $overrides['description'],
            'price' => $data['price'],
            'thumbnail' => $this->images->replace(
                $listing->thumbnail,
                $data['thumbnail'] ?? null,
                (bool) ($data['remove_thumbnail'] ?? false),
                'listings'
            ),
            'is_active' => $data['is_active'] ?? $listing->is_active,
        ]);

        return $listing->refresh()->load(['product', 'shop:id,name,slug']);
    }

    public function detach(ShopProduct $listing): void
    {
        $this->images->delete($listing->thumbnail);
        $listing->delete();
    }

    private function overrides(Product $product, array $data): array
    {
        $title = isset($data['title']) ? trim($data['title']) : '';
        $description = isset($data['description']) ? trim($data['description']) : '';

        return [
            'title' => ($title === '' || $title === $product->name) ? null : $title,
            'description' => ($description === '' || $description === $product->description) ? null : $description,
        ];
    }
}
