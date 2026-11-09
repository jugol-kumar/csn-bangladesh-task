<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\ProductRequest;
use App\Http\Resources\ProductResource;
use App\Models\Product;
use App\Services\ProductService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Response;

class ProductController extends Controller
{
    public function __construct(private ProductService $products)
    {
    }

    public function index(Request $request): AnonymousResourceCollection
    {
        return ProductResource::collection(
            $this->products->paginate(
                (string) $request->query('search', ''),
                (string) $request->query('source', '')
            )
        );
    }

    public function store(ProductRequest $request): JsonResponse
    {
        $product = $this->products->create($request->validated(), $request->user());

        return (new ProductResource($product))->response()->setStatusCode(201);
    }

    public function show(Product $product): ProductResource
    {
        return new ProductResource($product->load('creator:id,name')->loadCount('listings'));
    }

    public function update(ProductRequest $request, Product $product): ProductResource
    {
        return new ProductResource($this->products->update($product, $request->validated()));
    }

    public function destroy(Product $product): Response
    {
        $this->products->delete($product);

        return response()->noContent();
    }
}
