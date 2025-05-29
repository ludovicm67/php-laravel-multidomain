<?php

declare(strict_types=1);

use ludovicm67\Laravel\Multidomain\Configuration;
use PHPUnit\Framework\TestCase;

final class ConfigurationTest extends TestCase {

  protected function setUp(): void {
    parent::setUp();
    $this->resetConfigurationSingleton();
  }

  protected function tearDown(): void {
    $this->resetConfigurationSingleton();
  }

  private function resetConfigurationSingleton(): void {
    $ref = new ReflectionClass(Configuration::class);
    $instanceProp = $ref->getProperty('instance');
    $instanceProp->setAccessible(true);
    $instanceProp->setValue(null, null);
  }

  public function testCanInitialize(): void {
    $config = Configuration::getInstance('tests/fixtures/config.test.yaml');
    $this->assertNotNull($config->get());
  }

  public function testCanGetConfig(): void {
    $config = Configuration::getInstance('tests/fixtures/config.test.yaml');
    $configObject = $config->get();
    $parsedConfig = $configObject->get();
    $this->assertEquals('http://localhost/', $parsedConfig->fallback_url);
  }

  public function testCanGetDomainConfig(): void {
    $config = Configuration::getInstance('tests/fixtures/config.test.yaml');
    $configObject = $config->getDomain();
    $parsedConfig = $configObject->get();
    $this->assertNull($parsedConfig);
  }

  public function testCanGetSpecificDomainConfig(): void {
    $oldHost = $_SERVER['HTTP_HOST'] ?? false;
    $_SERVER['HTTP_HOST'] = 'localhost';

    $config = Configuration::getInstance('tests/fixtures/config.test.yaml');
    $configObject = $config->getDomain();
    $parsedConfig = $configObject->get();

    $this->assertNotNull($parsedConfig);
    $this->assertEquals('Localhost', $parsedConfig->site_name);

    if ($oldHost) {
      $_SERVER['HTTP_HOST'] = $oldHost;
    } else {
      unset($_SERVER['HTTP_HOST']);
    }
  }

  public function testCanGetSpecificAnotherDomainConfig(): void {
    $oldHost = $_SERVER['HTTP_HOST'] ?? false;
    $_SERVER['HTTP_HOST'] = 'amazing.localhost';

    $config = Configuration::getInstance('tests/fixtures/config.test.yaml');
    $configObject = $config->getDomain();
    $parsedConfig = $configObject->get();

    $this->assertNotNull($parsedConfig);
    $this->assertEquals('Amazing!', $parsedConfig->site_name);

    if ($oldHost) {
      $_SERVER['HTTP_HOST'] = $oldHost;
    } else {
      unset($_SERVER['HTTP_HOST']);
    }
  }
}
