<?php

namespace ludovicm67\Laravel\Multidomain\Exception;

class MultidomainException extends \Exception {
  public function __construct($message = '') {
    parent::__construct($message);
  }
}
