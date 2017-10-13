<?php

namespace Drupal\restapi;

use Drupal\restapi\Auth\AuthenticationServiceInterface;
use Psr\Http\Message\RequestInterface;
use stdClass;


/**
 * An interface for a configuration object for a resource.
 *
 * The configuration object holds metadata about the resource, and acts as a
 * factory for the main resource class and its associated authentication
 * handler.
 *
 */
interface ResourceConfigurationInterface {

  /**
   * Returns the class name for this resource.
   *
   * @return string
   *
   */
  public function getClass();


  /**
   * Returns the module that defined this resource.
   *
   * @return string
   *
   */
  public function getModule();


  /**
   * Returns the raw path of this resource.
   *
   * @return string
   *
   */
  public function getPath();


  /**
   * Returns an array of mimetypes that require versioning for this resource.
   *
   * @return array
   *
   */
  public function getVersionedTypes();


  /**
   * Factory method to instantiate the authentication service.
   *
   * @param stdClass $user
   *   A Drupal user object to access the resource as.
   * @param RequestInterface $request
   *   A HTTP request to set context for the authentication.
   *
   * @return AuthenticationServiceInterface
   *
   */
  public function invokeAuthenticationService(stdClass $user, RequestInterface $request);


  /**
   * Factory method to instantiate the resource.
   *
   * @param stdClass $user
   *   A Drupal user object to access the resource as.
   * @param RequestInterface $request
   *   A HTTP request to set context for the resource.
   *
   * @return ResourceInterface
   *
   */
  public function invokeResource(stdClass $user, RequestInterface $request);


  /**
   * Determines if this resource will be matched to the provided path.
   *
   * The resource will match either a raw path (e.g. "items/%/thing") or a real
   * path (e.g. "items/123/thing".
   *
   * @param string $path
   *   The path to attempt to match to this resource.
   *
   * @return boolean
   *
   */
  public function matchesPath($path);
}
