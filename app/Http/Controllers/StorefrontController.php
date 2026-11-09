<?php

namespace App\Http\Controllers;

use App\Http\Resources\ListingResource;
use App\Http\Resources\ShopResource;
use App\Models\Shop;
use App\Models\ShopProduct;
use App\Services\StorefrontService;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class StorefrontController extends Controller
{
    public function __construct(private StorefrontService $store)
    {
    }

    public function shops(Request $request): AnonymousResourceCollection
    {
        return ShopResource::collection(
            $this->store->shops((string) $request->query('search', ''))
        );
    }

    public function shop(Shop $shop): ShopResource
    {
        abort_unless($shop->is_active, 404);

        return new ShopResource($this->store->shop($shop));
    }

    public function products(Request $request, Shop $shop): AnonymousResourceCollection
    {
        abort_unless($shop->is_active, 404);

        return ListingResource::collection(
            $this->store->products(
                $shop,
                (string) $request->query('search', ''),
                (string) $request->query('sort', '')
            )
        );
    }

    public function latest(Request $request): AnonymousResourceCollection
    {
        return ListingResource::collection(
            $this->store->latest(
                (string) $request->query('search', ''),
                (string) $request->query('sort', '')
            )
        );
    }

    public function product(Shop $shop, ShopProduct $listing): ListingResource
    {
        abort_unless($shop->is_active, 404);

        $visible = $this->store->listing($shop, $listing);

        abort_if($visible === null, 404);

        return new ListingResource($visible);
    }
}
