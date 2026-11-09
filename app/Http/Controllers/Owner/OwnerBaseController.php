<?php

namespace App\Http\Controllers\Owner;

use App\Http\Controllers\Controller;
use App\Models\Shop;
use Illuminate\Http\Request;

abstract class OwnerBaseController extends Controller
{
    protected function shop(Request $request): Shop
    {
        $shop = $request->user()->shop;

        abort_if($shop === null, 409, 'No shop has been assigned to your account yet.');

        return $shop;
    }
}
