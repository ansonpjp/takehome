<?php

namespace App\Http\Controllers;

use App\Http\Requests\PurchaseOrderTotalsRequest;
use App\Services\PurchaseOrderService;
use Illuminate\Http\Request;

class PurchaseOrderController extends Controller
{
    /**
     * generate purchase order totals
     *
     * @param  Request $request
     */
    public function purchaseOrderTotals(PurchaseOrderTotalsRequest $request)
    {
        $purchaseOrderIds = array_map('intval', $request->input('purchase_order_ids')); //converts the elements of array to Integer
        $purchaseOrderService = new PurchaseOrderService();
        $data = $purchaseOrderService->calculateTotal($purchaseOrderIds);

        return response()->json($data);
    }
}
