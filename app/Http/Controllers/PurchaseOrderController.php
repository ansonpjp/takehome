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
        $purchaseOrderService = new PurchaseOrderService();
        return $purchaseOrderService->calculateTotal($request->all());

    }
}
