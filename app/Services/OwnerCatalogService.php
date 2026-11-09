<?php

namespace App\Services;

use App\Models\Product;
use App\Models\Shop;
use App\Models\ShopProduct;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class OwnerCatalogService
{
    public function __construct(
        private ProductService $products,
        private ListingService $listings
    ) {
    }

    public function createAndList(User $user, Shop $shop, array $data): ShopProduct
    {
        return DB::transaction(function () use ($user, $shop, $data) {
            $product = $this->products->create($data, $user);

            return $this->listings->attach($shop, $product, ['price' => $data['base_price']]);
        });
    }

    public function deleteOwn(Shop $shop, Product $product): void
    {
        $usedElsewhere = $product->listings()->where('shop_id', '!=', $shop->id)->exists();

        if ($usedElsewhere) {
            throw ValidationException::withMessages([
                'product' => ['Other shops already list this product, so it cannot be deleted.'],
            ]);
        }

        $this->products->delete($product);
    }
}
