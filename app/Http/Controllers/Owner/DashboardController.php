<?php

namespace App\Http\Controllers\Owner;

use App\Http\Resources\ShopResource;
use App\Services\DashboardService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class DashboardController extends OwnerBaseController
{
    public function __invoke(Request $request, DashboardService $dashboard): JsonResponse
    {
        $user = $request->user();
        $shop = $user->shop;

        return response()->json([
            'shop' => $shop ? new ShopResource($shop) : null,
            'counters' => $shop ? $dashboard->owner($user, $shop) : null,
        ]);
    }
}
