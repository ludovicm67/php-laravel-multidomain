<?php

namespace ludovicm67\Laravel\Multidomain;

class ConfigurationObject {
  private $data = null;

  /**
   * Constructor, init object with some data
   */
  public function __construct($data = null) {
    if (!is_array($data)) {
      $this->data = $data;
    } else {
      $this->data = (object) $data;
    }
  }

  /**
   * Get property value
   */
  public function get($key = null) {
    if (is_null($key) || !property_exists($this->data, $key)) {
      return null;
    }
    if (is_object($this->data->$key)) {
      return new static($this->data->$key);
    }
    return $this->data->$key;
  }
}
