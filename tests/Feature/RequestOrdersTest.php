<?php

namespace Tests\Feature;

use App\Models\Products;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Response as HttpResponse;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class RequestOrdersTest extends TestCase {
  use RefreshDatabase;
  protected $token;
  protected $test_order;
  public function setUp(): void {
    parent::setUp();
    DB::statement('ALTER SEQUENCE orders_id_seq RESTART WITH 1');
    DB::statement('ALTER SEQUENCE planet_destinations_id_seq RESTART WITH 1');
    $this->seed(DatabaseSeeder::class);
    $this->token      = auth()->attempt(['email' => 'admin@admin.com', 'password' => 'admin']);
    $ids              = Products::where('availability', true)->pluck('id')->toArray();
    $this->test_order = [
      'products'           => [
        [
          'product_id' => $ids[1],
          'amount'     => 2,
        ],
        [
          'product_id' => $ids[5],
          'amount'     => 4,
        ],
      ],
      'payment_method'     => 'Crypto',
      'planet_destination' => 2,
    ];
    $this->assertNotNull($this->token);
  }

  public function test_create_order(): void {
    $response = $this->withHeader('Authorization', 'Bearer ' . $this->token)->post('/api/orders', $this->test_order);
    $response->assertStatus(HttpResponse::HTTP_OK);
    $this->assertEquals('Pending', $response->json()['status']);
  }

  public function test_client_orders(): void {
    $local_token = auth()->attempt(['email' => 'admin@admin.com', 'password' => 'admin']);
    $this->withHeader('Authorization', 'Bearer ' . $local_token)->post('/api/orders', $this->test_order);
    $response = $this->withHeader('Authorization', 'Bearer ' . $local_token)->get('/api/orders');
    $response->assertStatus(HttpResponse::HTTP_OK);
    $this->assertEquals('Pending', $response->json()[0]['status']);
  }

  public function test_user_order_by_id(): void {
    $this->withHeader('Authorization', 'Bearer ' . $this->token)->post('/api/orders', $this->test_order);
    $response = $this->withHeader('Authorization', 'Bearer ' . $this->token)->get('/api/orders/1');
    $response->assertStatus(HttpResponse::HTTP_OK);
    $this->assertEquals('Pending', $response->json()['status']);
  }

  public function test_get_all_orders(): void {
    $this->withHeader('Authorization', 'Bearer ' . $this->token)->post('/api/orders', $this->test_order);
    $response = $this->withHeader('Authorization', 'Bearer ' . $this->token)->get('/api/orders/all');
    $response->assertStatus(HttpResponse::HTTP_OK);
    $this->assertEquals(1, $response->json()['total']);
  }

  public function test_delete_one_order(): void {
    $order    = $this->withHeader('Authorization', 'Bearer ' . $this->token)->post('/api/orders', $this->test_order);
    $id       = $order->json()['id'];
    $response = $this->withHeader('Authorization', 'Bearer ' . $this->token)->delete('/api/orders/' . $id);
    $response->assertStatus(HttpResponse::HTTP_OK);
    $this->assertEquals(true, $response->json());
  }

  public function test_update(): void {
    $order   = $this->withHeader('Authorization', 'Bearer ' . $this->token)->post('/api/orders', $this->test_order);
    $id      = $order->json()['id'];
    $request = $this->withHeader('Authorization', 'Bearer ' . $this->token)->patch('/api/orders/' . $id, ['status' => 'Finished']);
    $request->assertStatus(HttpResponse::HTTP_OK);
    $this->assertEquals('Finished', $request->json()['status']);
  }

  public function test_search_query(): void {
    $order       = $this->withHeader('Authorization', 'Bearer ' . $this->token)->post('/api/orders', $this->test_order);
    $total_price = $order->json()['total'];
    $greater     = $this->withHeader('Authorization', 'Bearer ' . $this->token)->get('/api/orders/search?total[gt]='.$total_price);
    $greater->assertStatus(HttpResponse::HTTP_OK);
    $this->assertEquals(0, $greater->json()['total']);
    $equal     = $this->withHeader('Authorization', 'Bearer ' . $this->token)->get('/api/orders/search?total[eq]='.$total_price);
    $equal->assertStatus(HttpResponse::HTTP_OK);
    $this->assertEquals(1, $equal->json()['total']);
  }
}
