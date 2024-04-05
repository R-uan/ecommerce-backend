<?php

namespace Tests\Feature;

use Database\Seeders\UserSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Response as HttpResponse;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class RequestAddressesTest extends TestCase {

  use RefreshDatabase;
  protected $token;
  public function setUp(): void {
    DB::statement('ALTER SEQUENCE user_id_seq RESTART WITH 1');
    $this->seed(UserSeeder::class);
    $this->token = auth()->attempt(['email' => 'admin@admin.com', 'password' => 'admin']);
    $this->assertNotNull($this->token);
  }

  public function test_create() {
    $address = [
      'planet'       => 'earth',
      'nation'       => 'brazil',
      'state'        => 'bahia',
      'city'         => 'salvador',
      'sector'       => '5',
      'residence_id' => '123',
    ];
    $response = $this->withHeader('Authorization', 'Bearer ' . $this->token)->post('/api/address/one', $address);
    $response->assertStatus(HttpResponse::HTTP_CREATED);
  }

  public function test_get_one(): void {

  }
}
