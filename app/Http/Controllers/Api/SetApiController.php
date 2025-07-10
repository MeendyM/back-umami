<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Set;
use Illuminate\Http\Request;

class SetApiController extends Controller
{
    public function getSets()
    {
        $sets = Set::with('products:id_product')->get(['id_set', 'name', 'description']);

        $sets = $sets->map(function ($set) {
            return [
                'id_set' => $set->id_set,
                'name' => $set->name,
                'description' => $set->description,
                'product_ids' => $set->products->pluck('id_product'),
            ];
        });

        return response()->json(['sets' => $sets], 200);
    }

    public function getProductsFromSets($id_set)
    {
        $set = Set::with('products')->find($id_set);

        if (!$set) {
            return response()->json(['message' => 'Set not found'], 404);
        }

        return response()->json(['set' => $set], 200);
    }
}
