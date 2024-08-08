<?php

namespace Tests\Feature;

use App\Services\PurchaseOrderService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Http\Client\Pool;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;
use App\Models\User;

class PurchaseOrderTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        //create user and assign token for SANCTUM
        $this->user = User::factory()->create([
            'password' => Hash::make('password')
        ]);
        $this->token = $this->user->createToken('token')->plainTextToken;
    }
    /**
     * Test api auth
     * Test the endpoint requires authentication and rejects any uun authenticated requests
     * Asserts to response status 401- shows UNAUTHENTICATED- TEST is PASSED
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

    /*
     * Test for requests with Authorization header that includes a Bearer TOKEN can pass
     * Asserts to response status code 200 - shows SUCCESS - TEST is PASSED
     */
    public function test_can_request_purchase_order_totals()
    {
        $response = $this->withHeaders([
            'Authorization' => 'Bearer '. $this->token,
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
                ],
                'failedRequests'
            ]);
    }
}
