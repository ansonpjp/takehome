<?php

namespace App\Http\Controllers;

use App\Factories\CalculatorFactory;
use App\Http\Requests\PurchaseOrderTotalsRequest;
use App\Services\PurchaseOrderService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Nette\InvalidArgumentException;

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
        $responses = collect($promises)->map(function ($promise) {
            try{
                return $promise->wait();
            } catch (\Exception $e){
                Log::channel('slack')->error('Failed to fetch purchase order: ' . $e->getMessage());
                return null;
            }
        });

        $totals = [
            1 => 0.0,
            2 => 0.0,
            3 => 0.0
        ];

        $failedRequests = [];
        foreach ($responses as $key => $response) {
            if($response && $response->successful()){
                $json = $response->json();
                $products = $json['data']['PurchaseOrderProduct'];

                foreach ($products as $product) {
                    try {
                        $calculator = CalculatorFactory::getCalculator($product['product_type_id']);
                        $totals[$product['product_type_id']] += $calculator->calculate($product);
                    } catch (InvalidArgumentException $e){
                        Log::channel('slack')->error('Failed to fetch purchase order: ' . $e->getMessage());
                    }
                }
            }
            else{
                $failedRequests[] = $request->purchase_order_ids[$key];
                $json = $response ? $response->json() : null;
                Log::channel('slack')->error('Failed to fetch purchase order: ' . ($response ? $response->status() : 'No response') . '. Error message: ' . json_encode($json['info'] ?? '') . ' (for Purchase Order ID: ' . $request->purchase_order_ids[$key] . ')');
            }
        }

        $result = [];
        foreach ($totals as $productTypeId => $total) {
            $result[] = [
                'product_type_id' => $productTypeId,
                'total' => number_format((float)$total,1)
            ];
        }

        return response()->json([
            'result' => $result,
            'failedRequests' => $failedRequests
        ]);
    }
}
