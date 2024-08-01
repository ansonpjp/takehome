<?php

namespace App\Http\Controllers;

use App\Http\Requests\PurchaseOrderTotalsRequest;
use App\Services\PurchaseOrderService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class PurchaseOrderController extends Controller
{
    /**
     * generate purchase order totals
     *
     * @param  Request $request
     */
    public function purchaseOrderTotals(PurchaseOrderTotalsRequest $request)
    {
        $promises = [];
        foreach ($request->purchase_order_ids as $purchaseOrderId) {
            $promises[] = Http::withBasicAuth('interview-test@cartoncloud.com.au', 'test123456')
               ->async()
               ->get('https://api.cartoncloud.com.au/CartonCloud_Demo/PurchaseOrders/' . $purchaseOrderId . '?version=5&associated=true');
        }
        // Wait to complete all REST CALLS
        $responses  = collect($promises)->map(function ($promise) {
            return $promise->wait();
        });

        $grouped = [];
        $productTypeOneTotal = 0;
        $productTypeTwoTotal = 0;
        $productTypeThreeTotal = 0;


        foreach ($responses  as $response) {
            if ($response->successful()) {
                $json = $response->json();
                $products = $json['data']['PurchaseOrderProduct'];

                foreach ($products as $product) {

                    $grouped[$product['product_type_id']] = $product;
                    if ($product['product_type_id'] == 1) {
                        $productTypeOneTotal = $productTypeOneTotal + ($product['unit_quantity_initial'] * $product['Product']['weight']);
                    } else if ($product['product_type_id'] == 2) {
                        $productTypeTwoTotal = $productTypeTwoTotal + ($product['unit_quantity_initial'] * $product['Product']['volume']);
                    } else if ($product['product_type_id'] == 3) {
                        $productTypeThreeTotal = $productTypeThreeTotal + ($product['unit_quantity_initial'] * $product['Product']['weight']);
                    }
                }
            }
        }

        $result = [
            [
                'product_type_id' => 1,
                'total' => $productTypeOneTotal,
            ],
            [
                'product_type_id' => 2,
                'total' => $productTypeTwoTotal,
            ],
            [
                'product_type_id' => 3,
                'total' => $productTypeThreeTotal,
            ]
        ];

        return response()->json([
            'result' => $result
        ]);
    }
}
