<?php

namespace ludovicm67\Laravel\Multidomain;

use \Symfony\Component\Yaml\Yaml;
use \ludovicm67\Laravel\Multidomain\Exception\MultidomainException;

class Configuration {
  private static $instance = null;
  private $configFile = null;
  private $config = null;
  private $domainConfig = null;

  /**
   * The contructor, called only once!
   */
  private function __construct($configFile) {
    $configValues = $this->parseFile($configFile);

    // if we are in cli, do nothing more
    if (!isset($_SERVER) || !isset($_SERVER['HOST'])) {
      return;
    }

    $this->fetchCurrentDomainConfiguration();
  }

  /**
   * Parse file and fill properties
   */
  private function parseFile($filename) {
    if (!file_exists($filename)) {
      throw new MultidomainException('Missing config.yml file.');
    }
    $this->configFile = $filename;

    // get configuration values
    $configValues = json_decode(json_encode(
        Yaml::parseFile($filename)
    ));
    $this->config = $configValues;

    return $configValues;
  }

  /**
   * Get current host's configuration
   */
  private function fetchCurrentDomainConfiguration() {
    // are some supported domains defined?
    if (!isset($this->config->supported_domains) ||
      count($this->config->supported_domains) <= 0) {
      throw new MultidomainException(
        'No supported domains defined in config.yml file'
      );
    }
    $askedHost = explode(':', $_SERVER['HTTP_HOST'])[0];

    // check if host is defined in configuration file
    if (!property_exists($this->config->supported_domains, $askedHost)) {
      if (!isset($this->config->fallback_url) ||
        empty($this->config->fallback_url)) {
        throw new Exception('This domain is not configured for the moment.');
      } else {
        header('Location: ' . $this->config->fallback_url);
        die();
      }
    }
    $this->domainConfig = $configValues->supported_domains->$askedHost;
  }

  /**
   * Just prevent cloning this object.
   */
  private function __clone() {
    // do nothing here
  }

  /**
   * Get the unique instance of this class
   */
  public static function getInstance($configFile) {
    if (is_null(static::$instance)) {
      static::$instance = new static($configFile);
    }
    return static::$instance;
  }

  /**
   * Returns all the configuration
   */
  public function get() {
    return $this->$config;
  }

  /**
   * Returns the configuration for the domain (or null if not found)
   */
  public function getDomain() {
    return $this->domainConfig;
  }
}
