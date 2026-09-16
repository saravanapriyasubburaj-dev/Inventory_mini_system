<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Http\JsonResponse;

class ProductController extends Controller
{

public function index(): JsonResponse
    {
        $products = Product::orderBy('id', 'asc')->get();

        return response()->json([
            'count' => $products->count(),
            'data'  => $products,
        ], 200);
    }
    
    public function lowStock(): JsonResponse
    {
        $threshold = config('shop.low_stock_threshold', 10);

        $products = Product::where('stock_on_hand', '<', $threshold)
            ->orderBy('stock_on_hand', 'asc')
            ->get();

        return response()->json([
            'threshold' => $threshold,
            'count'     => $products->count(),
            'data'      => $products,
        ]);
    }
}