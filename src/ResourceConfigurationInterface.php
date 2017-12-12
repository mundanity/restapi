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


  /**
   * Retrieves the deprecation information for an endpoint.
   *
   * Deprecation can be specified on an endpoint method by using the @deprecated
   * annotation. Examples:
   *
   * <code>
   *   @deprecated v1 This endpoint is no longer necessary.
   *   @deprecated 2
   *   @deprecated This endpoint is no longer necessary.
   *   @deprecated 3 This endpoint is no longer necessary.
   * </code>
   *
   * @param string $method
   *   The request method to check.
   *
   * @return array|NULL
   *   An associative array containing deprecation information if the
   *   endpoint is deprecated, NULL otherwise. Possible values are:
   *     - version: the version the endpoint has been deprecated since
   *     - reason: the reason the endpoint has been deprecated
   *
   */
  public function getDeprecationForMethod($method);


  /**
   * Retrieves the stability information for an endpoint.
   *
   * Stability can be specified on an endpoint method by using the @stability
   * annotation. Example:
   *
   * <code>
   *   @stability prototype
   * </code>
   *
   * If a stability is not explicitly specified, "production" will be assumed.
   *
   * @param string $method
   *   The request method to check.
   *
   * @return string
   *   The stability of the endpoint.
   *
   */
  public function getStabilityForMethod($method);

}
