<?php
namespace App\Services;

use App\Factories\CalculatorFactory;
use Illuminate\Support\Facades\Log;
use Nette\InvalidArgumentException;

/*
 * Class PurchaseOrderService
 * All logics related to purchaseOrders
 */
class PurchaseOrderService extends BaseWebService
{
    /*
     *  function to fetch the product details and calculate the total grouped by product_type_id
     */
    public function calculateTotal(array $data)
    {
        //  async call
        $promises = $this->createAsyncRequests($data['purchase_order_ids']);

        // handle the responses
        $responses = $this->handleAsyncResponse($promises);

        //process the responses
        $result = $this->processResponse($responses, $data['purchase_order_ids']);

        //prepare the results
        return $this->prepareResult($result);

    }

    /*
     * call the ASYNC call for all the purchaseOrderIds
     */
    public function createAsyncRequests(array $purchaseOrderIds)
    {
        return array_map(function ($purchaseOrderId) {
           return $this->makeAsyncRequest('/CartonCloud_Demo/PurchaseOrders/' . $purchaseOrderId . '?version=5&associated=true');
        }, $purchaseOrderIds);
    }

    /*
     * Process the response to get the Totals and Failed Requests
     */
    public function processResponse($responses, array $purchaseOrderIds)
    {
        $totals = [
          1 => 0.0,
          2 => 0.0,
          3 => 0.0
        ];
        $failedRequests = [];

        foreach ($responses as $key => $response) {
            if($response && $response->successful()){
                $this->processSuccess($response, $totals);
            } else{
                $failedRequests[] = $purchaseOrderIds[$key];
                $this->handleFailure($response, $purchaseOrderIds[$key]);
            }
        }
        return [
            'totals' => $totals,
            'failedRequests' => $failedRequests
        ];
    }

    /*
     * Process the success by calling the Calculators
     */
    public function processSuccess($response, array &$totals)
    {
        $json = $response->json();
        $products = $json['data']['PurchaseOrderProduct'];

        foreach ($products as $product) {
            try {
                $calculator = CalculatorFactory::getCalculator($product['product_type_id']);
                if($calculator){
                    $totals[$product['product_type_id']] += $calculator->calculate($product);
                }
            }catch(InvalidArgumentException $e){
                Log::channel('slack')->error('Error in product calculation: ' . $e->getMessage());
            }
        }
    }

    /*
     * Log the Failures to Slack
     */
    public function handleFailure($response, $purchaseOrderId)
    {
        $json = $response ? $response->json() : null;
        Log::channel('slack')->error('Failed to fetch purchase order: ' . ($response ? $response->status() : 'No response. ') .' Error message: ' . json_encode($json['info'] ?? '') . '( for purchase order ID:  )' . $purchaseOrderId . ')');
    }

    /*
     * Prepare the result as an array containing results and failedrequests
     */
    public function prepareResult(array $data)
    {
        $result =[];
        foreach ($data['totals'] as $productTypeId => $total) {
            $result[] = [
              'product_type_id' => $productTypeId,
              'total' => number_format((float)$total,1)
            ];
        }

        return [
            'result' => $result,
            'failedRequests' => $data['failedRequests']
        ];
    }
}
