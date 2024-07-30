<?php

namespace Tests\Feature;

use App\Services\PurchaseOrderService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Http\Client\Pool;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class PurchaseOrderTest extends TestCase
{
    /**
     * Test api auth
     *
     * @return void
     */
    public function test_api_requires_auth()
    {
        $response = $this->postJson(
            '/api/test',
            ['purchase_order_ids' => [2344]]
        );

        $response->assertStatus(401);
    }

    public function test_can_request_purchase_order_totals()
    {
        $response = $this->withHeaders([
            'Authorization' => 'Basic '. base64_encode("demo:pwd1234")
        ])->postJson(
            '/api/test',
            ['purchase_order_ids' => [2344]]
        );

        $response->assertStatus(200)
            ->assertJsonStructure([
                'result' => [
                    [
                        'product_type_id',
                        'total'
                    ],
                ]
            ]);
    }
}
