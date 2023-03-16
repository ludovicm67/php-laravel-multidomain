<?php

declare(strict_types=1);

use ludovicm67\Laravel\Multidomain\ConfigurationObject;
use PHPUnit\Framework\TestCase;

final class ConfigurationObjectTest extends TestCase {
  public function testCanHoldNullValue(): void {
    $config = new ConfigurationObject();
    $this->assertSame(null, $config->get());
  }

  public function testCanHoldArray(): void {
    $config = new ConfigurationObject([
      'fallback_url' => 'http://localhost/',
    ]);
    $this->assertEquals((object) [
      'fallback_url' => 'http://localhost/'
    ], $config->get());
  }

  public function testCanHoldObject(): void {
    $config = new ConfigurationObject((object) [
      'fallback_url' => 'http://localhost/',
    ]);
    $this->assertEquals((object) [
      'fallback_url' => 'http://localhost/'
    ], $config->get());
  }

  public function testGetValue(): void {
    $config = new ConfigurationObject((object) [
      'fallback_url' => 'http://localhost/',
    ]);
    $this->assertEquals('http://localhost/', $config->get('fallback_url'));
  }

  public function testGetNullValue(): void {
    $config = new ConfigurationObject((object) [
      'fallback_url' => null,
    ]);
    $this->assertEquals(null, $config->get('fallback_url'));
  }

  public function testGetNonExistingKey(): void {
    $config = new ConfigurationObject((object) [
      'fallback_url' => 'http://localhost/',
    ]);
    $this->assertEquals(null, $config->get('some-random-key'));
  }
}
