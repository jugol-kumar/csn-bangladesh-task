<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Shop;
use App\Models\User;
use Illuminate\Http\JsonResponse;

class OptionController extends Controller
{
    public function owners(): JsonResponse
    {
        $owners = User::query()
            ->where('role', User::ROLE_OWNER)
            ->with('shop:id,owner_id')
            ->orderBy('name')
            ->get(['id', 'name', 'email']);

        return response()->json([
            'data' => $owners->map(fn (User $owner) => [
                'id' => $owner->id,
                'name' => $owner->name,
                'email' => $owner->email,
                'shop_id' => $owner->shop?->id,
            ])->values(),
        ]);
    }

    public function shops(): JsonResponse
    {
        return response()->json([
            'data' => Shop::query()->orderBy('name')->get(['id', 'name', 'owner_id']),
        ]);
    }
}
