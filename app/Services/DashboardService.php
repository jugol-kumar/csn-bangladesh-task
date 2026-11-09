<?php

namespace App\Services;

use App\Models\Product;
use App\Models\Shop;
use App\Models\ShopProduct;
use App\Models\User;

class DashboardService
{
    public function admin(): array
    {
        return [
            'shops' => Shop::count(),
            'owners' => User::where('role', User::ROLE_OWNER)->count(),
            'products' => Product::count(),
            'listings' => ShopProduct::count(),
            'unlisted' => Product::whereDoesntHave('listings')->count(),
            'customers' => User::where('role', User::ROLE_CUSTOMER)->count(),
        ];
    }

    public function owner(User $user, Shop $shop): array
    {
        return [
            'listings' => ShopProduct::where('shop_id', $shop->id)->count(),
            'active' => ShopProduct::where('shop_id', $shop->id)->where('is_active', true)->count(),
            'catalog' => Product::count(),
            'available' => Product::whereDoesntHave('listings', fn ($query) => $query->where('shop_id', $shop->id))->count(),
            'mine' => Product::where('created_by', $user->id)->count(),
        ];
    }
}
