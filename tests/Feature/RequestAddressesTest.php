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
  protected $test_address = [
    'planet'       => 'earth',
    'nation'       => 'brazil',
    'state'        => 'bahia',
    'city'         => 'salvador',
    'sector'       => '5',
    'residence_id' => '123',
  ];

  public function setUp(): void {
    parent::setUp();
    DB::statement('ALTER SEQUENCE users_id_seq RESTART WITH 1');
    DB::statement('ALTER SEQUENCE addresses_id_seq RESTART WITH 1');
    $this->seed(UserSeeder::class);
    $this->token = auth()->attempt(['email' => 'admin@admin.com', 'password' => 'admin']);
    $this->assertNotNull($this->token);
  }

  public function test_create(): void {
    $response = $this->withHeader('Authorization', 'Bearer ' . $this->token)->post('/api/address/create', $this->test_address);
    $response->assertStatus(HttpResponse::HTTP_CREATED);
  }

  public function test_get_one(): void {
    $this->withHeader('Authorization', 'Bearer ' . $this->token)->post('/api/address/create', $this->test_address);
    $response = $this->withHeader('Authorization', 'Bearer ' . $this->token)->get('api/address/one');
    $response->assertStatus(HttpResponse::HTTP_OK);
    $response_json = $response->json();
    $this->assertEquals([ ...$this->test_address, 'id' => 1], $response_json);
  }

  public function test_update(): void {
    $this->withHeader('Authorization', 'Bearer ' . $this->token)->post('/api/address/create', $this->test_address);
    $new_data = ['Planet' => 'Pluto'];
    $response = $this->withHeader('Authorization', 'Bearer ' . $this->token)->patch('/api/address/update', $new_data);
    $response->assertStatus(HttpResponse::HTTP_OK);
    $this->assertEquals(true, $response->json());
  }

  public function test_destroy(): void {
    $this->withHeader('Authorization', 'Bearer ' . $this->token)->post('/api/address/create', $this->test_address);
    $response = $this->withHeader('Authorization', 'Bearer ' . $this->token)->delete('/api/address/destroy');
    $response->assertStatus(HttpResponse::HTTP_OK);
    $this->assertEquals(true, $response->json());
  }
}
