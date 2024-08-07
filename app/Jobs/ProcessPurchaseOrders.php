<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Services\PurchaseOrderService;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use App\Mail\PurchaseOrderResultsMail;

class ProcessPurchaseOrders implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $purchaseOrderIds;
    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(array $purchaseOrderIds)
    {
        $this->purchaseOrderIds = $purchaseOrderIds;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle(PurchaseOrderService $purchaseOrderService)
    {
        $promises = $purchaseOrderService->createAsyncRequests($this->purchaseOrderIds);
        $response = $purchaseOrderService->handleAsyncResponse($promises);

        $result = $purchaseOrderService->processResponse($response, $this->purchaseOrderIds);

        $data = $purchaseOrderService->prepareResult($result);

        Mail::to('ansonpjp@gmail.com')->send(new PurchaseOrderResultsMail($data));
        Log::channel('slack_results')->info('Processed Purchase order through job: ' . json_encode($data));
    }
}
