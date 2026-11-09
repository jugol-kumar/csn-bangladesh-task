<?php

namespace App\Http\Controllers\Owner;

use App\Http\Requests\ProductRequest;
use App\Http\Resources\ListingResource;
use App\Http\Resources\ProductResource;
use App\Models\Product;
use App\Services\ListingService;
use App\Services\OwnerCatalogService;
use App\Services\ProductService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Response;

class CatalogController extends OwnerBaseController
{
    public function __construct(
        private ListingService $listings,
        private ProductService $products,
        private OwnerCatalogService $catalog
    ) {
    }

    public function index(Request $request): AnonymousResourceCollection
    {
        $scope = in_array($request->query('scope'), ['available', 'mine'], true)
            ? $request->query('scope')
            : 'all';

        return ProductResource::collection(
            $this->listings->catalog(
                $this->shop($request),
                $request->user()->id,
                (string) $request->query('search', ''),
                $scope
            )
        );
    }

    public function store(ProductRequest $request): JsonResponse
    {
        $listing = $this->catalog->createAndList(
            $request->user(),
            $this->shop($request),
            $request->validated()
        );

        return (new ListingResource($listing))->response()->setStatusCode(201);
    }

    public function update(ProductRequest $request, Product $product): ProductResource
    {
        $this->ownProduct($request, $product);

        return new ProductResource($this->products->update($product, $request->validated()));
    }

    public function destroy(Request $request, Product $product): Response
    {
        $this->ownProduct($request, $product);
        $this->catalog->deleteOwn($this->shop($request), $product);

        return response()->noContent();
    }

    private function ownProduct(Request $request, Product $product): void
    {
        abort_unless($product->created_by === $request->user()->id, 403, 'You can only change products you created.');
    }
}
