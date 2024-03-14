<?php

namespace Tests\Feature;

use App\Models\Products;
use Database\Seeders\DatabaseSeeder;
use Faker\Factory as FakerFactory;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Response;
use Tests\TestCase;

class RequestsProductsTest extends TestCase {
  use RefreshDatabase;

  public function setUp(): void {
    parent::setUp();
    \DB::statement('ALTER SEQUENCE products_id_seq RESTART WITH 1');
    $this->seed(DatabaseSeeder::class);
  }

  /**
   * Creates a token for the authentication:
   * - Expects the token to not be null.
   *
   * Sends a get request to /api/admin/products.
   * - Expects a CREATED (201) response.
   */
  public function test_create_one_product(): void {
    $faker = FakerFactory::create();

    $token = auth()->attempt(['email' => 'admin@admin.com', 'password' => 'admin']);
    $this->assertNotNull($token);

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
    $response = $this->withHeader('Authorization', 'Bearer ' . $token)->post('/api/admin/products', $product_data);
    $response->assertStatus(Response::HTTP_CREATED);
  }

  /**
   * Sends a get request to /api/products/ and
   * - Expects a OK (200) response.
   * - Expects a total of 100 products.
   */
  public function test_get_all_products(): void {
    $response = $this->get('/api/products');
    $response->assertStatus(Response::HTTP_OK);
    $response_json = $response->json();
    $product_total = $response_json['total'];
    $this->assertEquals(100, $product_total);
  }

  /**
   * Sends a get request to /api/products/{id}
   * - Expects a OK (200) response.
   */
  public function test_get_product_by_id(): void {
    $id       = mt_rand(1, 100);
    $response = $this->get('/api/products/' . $id);
    $response->assertStatus(Response::HTTP_OK);
    $response_json = $response->json();
    $product_id    = $response_json['id'];
    $this->assertEquals($id, $product_id);
  }
}
