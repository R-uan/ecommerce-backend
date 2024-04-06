<?php

namespace Tests\Feature;

use Database\Seeders\DatabaseSeeder;
use Faker\Factory as FakerFactory;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class RequestProductsTest extends TestCase {
  use RefreshDatabase;
  protected $token;

  /**
   * Resets the id sequence
   * Seeds the database
   *
   * Creates a token for the request that require authentication.
   * - Uses the user from the seeding to authenticate.
   * - Asserts the token is not null.
   */
  public function setUp(): void {
    parent::setUp();
    DB::statement('ALTER SEQUENCE products_id_seq RESTART WITH 1');
    DB::statement('ALTER SEQUENCE manufacturers_id_seq RESTART WITH 1');
    $this->seed(DatabaseSeeder::class);
    $this->token = auth()->attempt(['email' => 'admin@admin.com', 'password' => 'admin']);
    $this->assertNotNull($this->token);
  }

  /**
   * GET request to /api/products.
   * - Asserts that the response status code is OK (200).
   */
  public function test_create_one_product(): void {
    $faker        = FakerFactory::create();
    $product_data = [
      'image_url'         => $faker->url(),
      'category'          => $faker->name(),
      'name'              => $faker->name(),
      'availability'      => $faker->boolean(),
      'short_description' => $faker->text(100),
      'long_description'  => $faker->text(300),
      'production_time'   => $faker->randomDigit(),
      'unit_price'        => $faker->randomFloat(2),
      'manufacturers_id'  => 1,

      "product_details"   => [
        'propulsion_system'   => $faker->text(10),
        'external_structure'  => $faker->text(10),
        'energy_system'       => $faker->text(10),
        'comunication_system' => $faker->text(10),
        'navigation_system'   => $faker->text(10),
        'termic_protection'   => $faker->text(10),
        'emergency_system'    => $faker->text(10),
        'landing_system'      => $faker->text(10),
      ],
    ];
    $response = $this->withHeader('Authorization', 'Bearer ' . $this->token)->post('/api/products', $product_data);
    $response->assertStatus(Response::HTTP_CREATED);
  }

  /**
   * GET request to /api/products
   * - Asserts that the response status code is OK (200).
   * - Asserts that the total itens return is 100.
   */
  public function test_get_all_products(): void {
    $response = $this->get('/api/products');
    $response->assertStatus(Response::HTTP_OK);
    $response_json = $response->json();
    $product_total = $response_json['total'];
    $this->assertEquals(100, $product_total);
  }

  /**
   * GET request to /api/products/{id}
   * - Asserts that the response status code is OK (200).
   * - Generates a random number between 1-100 and uses it as the id.
   * - Asserts that the id from the product received is the same it sent.
   */
  public function test_get_product_by_id_request(): void {
    $id       = mt_rand(1, 100);
    $response = $this->get('/api/products/' . $id);
    $response->assertStatus(Response::HTTP_OK);
    $response_json = $response->json();
    $product_id    = $response_json['id'];
    $this->assertEquals($id, $product_id);
  }

  /**
   * GET request to /api/products/search?
   * - Asserts that the response status code is OK (200).
   * - Asserts that the total itens returned is higher than 0
   */
  public function test_search_query_request() {
    $response = $this->get('/api/products/search?name[lk]=dr');
    $response->assertStatus(200);
    $response_json = $response->json();
    $product_total = $response_json['total'];
    $this->assertGreaterThan(0, $product_total);
  }

  /**
   * UPDATE request to /api/products/{id}
   * - Asserts that the response status code is OK (200).
   * - Asserts that the returned product name was updated.
   */
  public function test_update_request() {
    $update_data = ['name' => 'svhEftyçP'];
    $request     = $this->withHeader('Authorization', 'Bearer ' . $this->token)->patch('/api/products/10', $update_data);
    $request->assertStatus(200);
    $request_json = $request->json();
    $this->assertEquals($request_json['name'], 'svhEftyçP');
  }

  /**
   * DELETE Request to /api/products/{id}
   * - Asserts that the return data is 1.
   * - Asserts that the response status code is OK (200).
   * Tries to find the deleted record in the database.
   * - Asserts that the response status code is NOT FOUND (404).
   */
  public function test_delete_request() {
    $id       = mt_rand(1, 100);
    $response = $this->withHeader('Authorization', 'Bearer ' . $this->token)->delete('/api/products/' . $id);
    $response->assertStatus(Response::HTTP_OK);
    $response_json = $response->json();
    $this->assertEquals($response_json, 1);

    $verify = $this->get('/api/products/' . $id);
    $verify->assertStatus(404);
  }

  /**
   * GET request to /api/products/miniatures
   * - Asserts that the response status code is OK (200).
   * - Asserts that the total itens return is 100.
   */
  public function test_get_all_miniatures() {
    $response = $this->get('/api/products/miniatures');
    $response->assertStatus(Response::HTTP_OK);
    $response_json = $response->json();
    $product_total = $response_json['total'];
    $this->assertEquals(100, $product_total);
  }

  /**
   * POST request to /api/products/miniatures
   * - Asserts that the response status code is OK (200).
   * - Asserts that the total itens return is 3.
   */
  public function test_get_some_miniatures() {
    $ids      = [5, 75, 35];
    $response = $this->post('/api/products/miniatures', ['products' => $ids]);
    $response->assertStatus(Response::HTTP_OK);
    $response_json = $response->json();
    $this->assertEquals(count($response_json), 3);
  }
}
