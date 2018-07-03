<?php

namespace ludovicm67\Laravel\Multidomain;

class Configuration {
  private static $instance = null;

  private function __construct() {
    // @TODO: do something here
  }

  private function __clone() {
    // do nothing here
  }

  public static function getInstance() {
    if (is_null(static::$instance)) {
      static::$instance = new static;
    }
    return static::$instance;
  }
}
