<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\ShopRequest;
use App\Http\Resources\ShopResource;
use App\Models\Shop;
use App\Services\ShopService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Response;

class ShopController extends Controller
{
    public function __construct(private ShopService $shops)
    {
    }

    public function index(Request $request): AnonymousResourceCollection
    {
        return ShopResource::collection(
            $this->shops->paginate((string) $request->query('search', ''))
        );
    }

    public function store(ShopRequest $request): JsonResponse
    {
        $shop = $this->shops->create($request->validated());

        return (new ShopResource($shop))->response()->setStatusCode(201);
    }

    public function show(Shop $shop): ShopResource
    {
        return new ShopResource($shop->load('owner:id,name,email')->loadCount('listings'));
    }

    public function update(ShopRequest $request, Shop $shop): ShopResource
    {
        return new ShopResource($this->shops->update($shop, $request->validated()));
    }

    public function destroy(Shop $shop): Response
    {
        $this->shops->delete($shop);

        return response()->noContent();
    }
}
