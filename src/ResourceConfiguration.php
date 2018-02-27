<?php

namespace Drupal\restapi;

use Drupal\restapi\Exception\ClassNotValidException;
use Drupal\restapi\Exception\AuthClassNotValidException;
use Drupal\restapi\Exception\ClassMethodNotValidException;
use Psr\Http\Message\RequestInterface;
use ReflectionClass;


/**
 * A configuration object for a resource.
 *
 * The configuration object holds metadata about the resource, and acts as a
 * factory for the main resource class and it's associated authentication
 * handler.
 *
 */
class ResourceConfiguration implements ResourceConfigurationInterface {

  /**
   * The raw path to the resource (e.g. items/%/thing).
   *
   * @var string
   *
   */
  protected $path = NULL;


  /**
   * An array of mimetypes that must be versioned for this resource.
   *
   * @var array
   *
   */
  protected $versioned_types = [
    'application/json',
  ];


  /**
   * The Drupal module that defined this resource.
   *
   * @var string
   *
   */
  protected $module = NULL;


  /**
   * The name of the class to be instantiated for this resource.
   *
   * @var string
   *
   */
  protected $class = NULL;


  /**
   * The name of the authentication class to use for this resource.
   *
   * @var string
   *
   */
  protected $auth_class = NULL;


  /**
   * The URL prefix to use for this resource.
   *
   * @var string
   *
   */
  protected $url_prefix = NULL;


  /**
   * Constructor
   *
   * @param string $path
   *   The raw path to the resource (e.g. items/%/thing).
   * @param string $module
   *   The module that defined this resource.
   * @param string $class
   *   The name of the class to be instantiated for this resource.
   * @param string $auth_class
   *   The name of the authentication class to use for this resource.
   * @param string $url_prefix
   *   (Optional) a string to use as the URL prefix for this resource.
   *
   * @throws ClassNotValidException
   * @throws AuthClassNotValidException
   *
   */
  public function __construct($path, $module, $class, $auth_class, $url_prefix = NULL) {

    if (!class_exists($class) || !in_array('Drupal\restapi\ResourceInterface', class_implements($class))) {
      $message = sprintf('The provided class %s does not exist, or is not an implementation of "Drupal\restapi\ResourceInterface".', $class);
      throw new ClassNotValidException($message);
    }

    if (!class_exists($auth_class) || !in_array('Drupal\restapi\Auth\AuthenticationServiceInterface', class_implements($auth_class))) {
      $message = sprintf('The provided authentication class %s does not exist, or is not an implementation of "Drupal\restapi\Auth\AuthenticationServiceInterface".', $class);
      throw new AuthClassNotValidException($message);
    }

    $this->path = $this->resolveTruePath($path, $url_prefix);
    $this->module = $module;
    $this->class = $class;
    $this->auth_class = $auth_class;
    $this->url_prefix = $url_prefix;

  }


  /**
   * {@inheritdoc}
   *
   */
  public function invokeResource(\StdClass $user, RequestInterface $request) {
    $class = $this->getClass();
    return new $class($user, $request);
  }


  /**
   * {@inheritdoc}
   *
   */
  public function invokeAuthenticationService(\StdClass $user, RequestInterface $request) {
    $class = $this->getAuthenticationClass();
    return new $class($user, $request);
  }


  /**
   * {@inheritdoc}
   *
   */
  public function getPath() {
    return $this->path;
  }


  /**
   * {@inheritdoc}
   *
   */
  public function getVersionedTypes() {
    return $this->versioned_types;
  }


  /**
   * {@inheritdoc}
   *
   */
  public function getClass() {
    return $this->class;
  }


  /**
   * {@inheritdoc}
   *
   */
  public function getModule() {
    return $this->module;
  }


  /**
   * Returns the authentication class for this resource.
   *
   * @returns string
   *
   */
  public function getAuthenticationClass() {
    return $this->auth_class;
  }


  /**
   * Returns a list of arguments for this resource, based on the provided path.
   *
   * @param string $path
   *   The path to generate arguments from.
   *
   * @return array
   *
   */
  public function getArgumentsForPath($path) {

    if (!$this->matchesPath($path)) {
      return [];
    }

    $arguments = [];
    $path      = explode('/', $path);

    foreach($this->getArgIndexes() as $index) {
      $arguments[] = $path[$index];
    }

    return $arguments;

  }


  /**
   * {@inheritdoc}
   *
   */
  public function matchesPath($path) {
    return ($this->getPath() == $path || preg_match($this->getMaskedPath(), $path));
  }


  /**
   * {@inheritdoc}
   *
   */
  public function getDeprecationForMethod($method) {
    $class = new ReflectionClass($this->getClass());

    if (!$class->hasMethod($method)) {
      return NULL;
    }

    $doc_comment = $class->getMethod($method)->getDocComment();
    $deprecated  = preg_match('/\* @deprecated(?:\h+(?:[vV]?([0-9]+))?)?(?:\h+(.*)?)?$/m', $doc_comment, $matches);

    if (!$deprecated) {
      return NULL;
    }

    return [
      'version' => isset($matches[1]) ? $matches[1] : NULL,
      'reason'  => isset($matches[2]) ? $matches[2] : NULL,
    ];
  }


  /**
   * {@inheritdoc}
   *
   */
  public function getStabilityForMethod($method) {

    $class = new ReflectionClass($this->getClass());

    if (!$class->hasMethod($method)) {
      return NULL;
    }

    $doc_comment = $class->getMethod($method)->getDocComment();
    $stability   = preg_match('/\* @stability\h+(.*)/m', $doc_comment, $matches);

    return $stability ? $matches[1] : 'production';
  }


  /**
   * Returns an array of integers corresponding to the index of variables
   * within the path.
   *
   * @return array
   *
   */
  protected function getArgIndexes() {
    $parts = explode('/', $this->getPath());
    $args  = [];

    foreach($parts as $index => $part) {
      if ($part === '%') {
        $args[] = $index;
      }
    }

    return $args;
  }


  /**
   * Returns the regex masked path for this resource.
   *
   * Essentially, replaces any variable substitutions with a regex pattern
   * matching the variable. (e.g. "items/%/thing" becomes
   * "/items\/[^/]*\/thing".
   *
   * @return string
   *
   */
  protected function getMaskedPath() {
    return '#^' . str_replace('%', '.[^/]*', $this->getPath()) . '$#';
  }


  /**
   * Determines the real path, if a URL prefix has been used.
   *
   * @param string $path
   *   The original path of the resource.
   * @param string $prefix
   *   The URL prefix to use for this path.
   *
   * @return string
   *
   */
  protected function resolveTruePath($path, $prefix = NULL) {

    $path   = ltrim(trim($path), '/');
    $prefix = rtrim(ltrim(trim($prefix), '/'), '/');

    // Map path of "ROOT" to the prefix.
    if ($path == "ROOT") {
      return $prefix ?: '/';
    }

    if (!$prefix) {
      return $path;
    }

    return $prefix . '/' . $path;

  }

}
