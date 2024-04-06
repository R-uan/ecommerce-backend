<?php

namespace Tests\Feature;

use Database\Seeders\UserSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Response as HttpResponse;
use Tests\TestCase;

class RequestAuthenticationTest extends TestCase {
  use RefreshDatabase;
  protected $test_credentials  = ['email' => 'admin@admin.com', 'password' => 'admin'];
  protected $false_credentials = ['email' => 'fake@fake.com', 'password' => 'notreal'];
  public function setUp(): void {
    parent::setUp();
    $this->seed(UserSeeder::class);
  }

  public function test_login(): void {
    $response = $this->post('/api/auth/login', $this->test_credentials);
    $response->assertStatus(HttpResponse::HTTP_OK);
  }

  public function test_refresh_token(): void {
    $login    = $this->post('/api/auth/login', $this->test_credentials);
    $token    = $login->json();
    $response = $this->withHeader('Authorization', 'Bearer ' . $token)->get('/api/auth/refresh');
    $response->assertStatus(HttpResponse::HTTP_OK);
  }

  public function test_failed_login(): void {
    $response = $this->post('/api/auth/login', $this->false_credentials);
    $response->assertStatus(HttpResponse::HTTP_UNAUTHORIZED);
  }

  public function test_failed_refresh_token(): void {
    $response = $this->withHeader('Authorization', 'Bearer ' . 'not real')->get('/api/auth/refresh');
    $response->assertStatus(HttpResponse::HTTP_UNAUTHORIZED);
  }
}
