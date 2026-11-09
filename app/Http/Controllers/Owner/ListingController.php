<?php

namespace App\Http\Controllers\Owner;

use App\Http\Requests\ListingRequest;
use App\Http\Resources\ListingResource;
use App\Models\Product;
use App\Models\ShopProduct;
use App\Services\ListingService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Response;

class ListingController extends OwnerBaseController
{
    public function __construct(private ListingService $listings)
    {
    }

    public function index(Request $request): AnonymousResourceCollection
    {
        return ListingResource::collection(
            $this->listings->forShop($this->shop($request), (string) $request->query('search', ''))
        );
    }

    public function store(ListingRequest $request): JsonResponse
    {
        $shop = $this->shop($request);
        $product = Product::findOrFail($request->validated('product_id'));

        $listing = $this->listings->attach($shop, $product, $request->validated());

        return (new ListingResource($listing))->response()->setStatusCode(201);
    }

    public function update(ListingRequest $request, ShopProduct $listing): ListingResource
    {
        $this->ownListing($request, $listing);

        return new ListingResource($this->listings->update($listing, $request->validated()));
    }

    public function destroy(Request $request, ShopProduct $listing): Response
    {
        $this->ownListing($request, $listing);
        $this->listings->detach($listing);

        return response()->noContent();
    }

    private function ownListing(Request $request, ShopProduct $listing): void
    {
        abort_unless($listing->shop_id === $this->shop($request)->id, 404);
    }
}
