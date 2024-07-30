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
        $responses = [];
        foreach ($request->purchase_order_ids as $purchaseOrderId) {
            $responses[] = Http::withBasicAuth('interview-test@cartoncloud.com.au', 'test123456')->get('https://api.cartoncloud.com.au/CartonCloud_Demo/' . $purchaseOrderId . '?version=5&associated=true');
        }

        $grouped = [];
        $productTypeOneTotal = 0;
        $productTypeTwoTotal = 0;
        $productTypeThreeTotal = 0;
        foreach ($responses as $response) {
            $json = $response->json();
            $products = $json['data']['PurchaseOrderProduct'];

            foreach ($products as $product) {

                $grouped[$product['product_type_id']] = $product;

                if ($product['product_type_id'] == 1) {
                    $productTypeOneTotal = $productTypeOneTotal + ($product['unit_quantity_initial'] * $product['weight']);
                } else if ($product['product_type_id'] == 2) {
                    $productTypeTwoTotal = $productTypeTwoTotal + ($product['unit_quantity_initial'] * $product['volume']);
                } else if ($product['product_type_id'] == 3) {
                    $productTypeThreeTotal = $productTypeThreeTotal + ($product['unit_quantity_initial'] * $product['weight']);
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
