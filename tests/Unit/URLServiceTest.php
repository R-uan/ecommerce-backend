<?php

namespace Tests\Unit;

use App\Services\URLService;
use PHPUnit\Framework\TestCase;

class URLServiceTest extends TestCase {
  public function test_slug_creation() {
    $name = "Criando um Slug Apartir de um Texto";
    $slug = URLService::CreateSlug($name);
    dump($slug);
    $this->assertEquals('criando-um-slug-apartir-de-um-texto', $slug);
  }
}
