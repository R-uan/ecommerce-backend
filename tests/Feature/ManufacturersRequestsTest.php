<?php

namespace Tests\Feature;

use Database\Seeders\DatabaseSeeder;
use Faker\Factory as FakerFactory;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class ManufacturersRequestsTest extends TestCase {
  use RefreshDatabase;
  protected $token;
  public function setUp(): void {
    parent::setUp();
    DB::statement('ALTER SEQUENCE manufacturers_id_seq RESTART WITH 1');
    $this->seed(DatabaseSeeder::class);
    $this->token = auth()->attempt(['email' => 'admin@admin.com', 'password' => 'admin']);
    $this->assertNotNull($this->token);
  }

  /**
   * GET request to /api/manufacturers.
   * - Asserts that the response status code is OK (200).
   */
  public function test_create_one_manufacturer(): void {
    $faker             = FakerFactory::create();
    $manufacturer_data = [
      'name'    => $faker->company(),
      'email'   => $faker->companyEmail(),
      'website' => $faker->url(),
    ];
    $response = $this->withHeader('Authorization', 'Bearer ' . $this->token)->post('/api/manufacturers', $manufacturer_data);
    $response->assertStatus(Response::HTTP_CREATED);
  }

  /**
   * GET request to /api/manufacturers
   * - Asserts that the response status code is OK (200).
   * - Asserts that the total itens return is 100.
   */
  public function test_get_all_manufacturers() {
    $response = $this->get('/api/manufacturers');
    $response->assertStatus(Response::HTTP_OK);
    $response_json = $response->json();
    $this->assertEquals(10, $response_json['total']);
  }

  /**
   * GET request to /api/manufacturers/{id}
   * - Asserts that the response status code is OK (200).
   * - Generates a random number between 1-10 and uses it as the id.
   * - Asserts that the id from the manufacturer received is the same it sent.
   */
  public function test_get_manufacturer_by_id_request(): void {
    $id       = mt_rand(1, 10);
    $response = $this->get('/api/manufacturers/' . $id);
    $response->assertStatus(Response::HTTP_OK);
    $response_json = $response->json();
    $product_id    = $response_json['id'];
    $this->assertEquals($id, $product_id);
  }

  /**
   * GET request to /api/manufacturers/search?
   * - Asserts that the response status code is OK (200).
   * - Asserts that the total itens returned is higher than 0
   */
  public function test_search_query_request() {
    $faker             = FakerFactory::create();
    $manufacturer_data = [
      'name'    => 'test',
      'email'   => $faker->companyEmail(),
      'website' => $faker->url(),
    ];
    $this->withHeader('Authorization', 'Bearer ' . $this->token)->post('/api/manufacturers', $manufacturer_data);

    $response = $this->get('/api/manufacturers/search?name[lk]=test');
    $response->assertStatus(200);
    $response_json = $response->json();
    $product_total = $response_json['total'];
    $this->assertGreaterThan(0, $product_total);
  }

  /**
   * UPDATE request to /api/manufacturers/{id}
   * - Asserts that the response status code is OK (200).
   * - Asserts that the returned product name was updated.
   */
  public function test_update_request() {
    $update_data = ['name' => 'svhEftyçP'];
    $request     = $this->withHeader('Authorization', 'Bearer ' . $this->token)->patch('/api/manufacturers/5', $update_data);
    $request->assertStatus(200);
    $request_json = $request->json();
    $this->assertEquals($request_json['name'], 'svhEftyçP');
  }

  /**
   * DELETE Request to /api/manufacturers/{id}
   * - Asserts that the return data is 1.
   * - Asserts that the response status code is OK (200).
   * Tries to find the deleted record in the database.
   * - Asserts that the response status code is NOT FOUND (404).
   */
  public function test_delete_request() {
    $id       = mt_rand(1, 10);
    $response = $this->withHeader('Authorization', 'Bearer ' . $this->token)->delete('/api/manufacturers/' . $id);
    $response->assertStatus(Response::HTTP_OK);
    $response_json = $response->json();
    $this->assertEquals($response_json, 1);

    $verify = $this->get('/api/manufacturers/' . $id);
    $verify->assertStatus(404);
  }

  /**
   * GET Request to /api/manufacturers/{id}/products
   * - Asserts that the return data array has 10 elements.
   * It only checks 10 elements because thats the amount the seeder creates for each manufacturer.
   */
  public function test_get_manufacturer_products() {
    $id       = mt_rand(1, 10);
    $response = $this->get('/api/manufacturers/' . $id . '/products');
    $response->assertStatus(Response::HTTP_OK);
    $response_json = $response->json();
    $this->assertEquals(10, count($response_json));
  }
}