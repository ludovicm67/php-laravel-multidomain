<?php

namespace ludovicm67\Laravel\Multidomain;

use \Symfony\Component\Yaml\Yaml;
use \ludovicm67\Laravel\Multidomain\ConfigurationObject;
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
    $this->parseFile($configFile);

    // if we are in cli, do nothing more
    if (!isset($_SERVER) || !isset($_SERVER['HTTP_HOST'])) {
      return;
    }

    $this->fetchCurrentDomainConfiguration();
  }

  /**
   * Parse file and fill properties
   */
  private function parseFile($filename) {
    if (is_null($filename) || !file_exists($filename)) {
      throw new MultidomainException('Missing config.yaml file.');
    }
    $this->configFile = $filename;

    // get configuration values
    $this->config = json_decode(json_encode(
        Yaml::parseFile($filename)
    ));
  }

  /**
   * Get current host's configuration
   */
  private function fetchCurrentDomainConfiguration() {
    // are some supported domains defined?
    if (!isset($this->config->supported_domains) ||
      empty($this->config->supported_domains)) {
      throw new MultidomainException(
        'No supported domains defined in config.yaml file'
      );
    }

    $askedHost = null;

    $host = explode(':', $_SERVER['HTTP_HOST'])[0];
    $hostWithoutApi = null;

    if (substr($host, 0, 4) == 'api.') { // if starts with 'api.'
      $hostWithoutApi = substr($host, 4); // remove the 'api.' prefix
    }

    // check if host is defined in configuration file
    if (property_exists($this->config->supported_domains, $host)) {
      $askedHost = $host;
    } else if (
      !is_null($hostWithoutApi) &&
      property_exists($this->config->supported_domains, $hostWithoutApi)
    ) {
      $askedHost = $hostWithoutApi;
    }

    if (
      is_null($askedHost) ||
      !property_exists($this->config->supported_domains, $askedHost)
    ) {
      if (!isset($this->config->fallback_url) ||
        empty($this->config->fallback_url)) {
        throw new Exception('This domain is not configured for the moment.');
      } else {
        header('Location: ' . $this->config->fallback_url);
        die();
      }
    }

    $this->domainConfig = $this->config->supported_domains->$askedHost;
  }

  /**
   * Just prevent cloning this object.
   */
  private function __clone() {
    // do nothing here
  }

  /**
   * Get the unique instance of this class
   * @return Configuration unique instance
   */
  public static function getInstance($configFile = null) {
    if (is_null(static::$instance)) {
      static::$instance = new static($configFile);
    }
    return static::$instance;
  }

  /**
   * Returns all the configuration
   * @return object global configuration
   */
  public function get() {
    return new ConfigurationObject($this->config);
  }

  /**
   * Returns the configuration for the domain (or null if not found)
   * @return object current domain configuration
   */
  public function getDomain() {
    return new ConfigurationObject($this->domainConfig);
  }
}
