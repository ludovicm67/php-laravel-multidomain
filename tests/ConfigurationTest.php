<?php

declare(strict_types=1);

use ludovicm67\Laravel\Multidomain\Configuration;
use PHPUnit\Framework\TestCase;

final class ConfigurationTest extends TestCase {
  public function testCanInitialize(): void {
    $config = Configuration::getInstance("tests/fixtures/config.test.yaml");
    $this->assertNotNull($config->get());
  }

  public function testCanGetConfig(): void {
    $config = Configuration::getInstance("tests/fixtures/config.test.yaml");
    $configObject = $config->get();
    $parsedConfig = $configObject->get();
    $this->assertEquals("http://localhost/", $parsedConfig->fallback_url);
  }
}
