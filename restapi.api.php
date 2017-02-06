<?php

use Drupal\restapi\ResourceConfiguration;
use Drupal\restapi\JsonRequest;
use Drupal\restapi\JsonResponse;
use Psr\Http\Message\ResponseInterface;
use Drupal\restapi\Exception\RestApiException;


/**
 * Defines resources that will be made available to Drupal.
 *
 * Resources are structured in a similar fashion to Drupal menu items, and have
 * the following configuration options:
 * - class: (required) The class that will handle this endpoint.
 * - auth: (optional) The class that will handle custom authentication. This
 *   class must implement Auth\AuthenticationServiceInterface. By default,
 *   resources without this option defined will use the
 *   DrupalAuthenticationService. This class can be configured via the
 *   "restapi_default_auth_class" variable.
 * - config: (optional) The class that will hold the custom configuration for
 *   the resource. By default, resource configurations are handled by
 *   ResourceConfiguration, but this allows any class that implements
 *   ResourceConfigurationInterface to handle its own configuration and thus
 *   resource invocation (via invokeResource method).
 *
 * @return array
 *
 */
function hook_restapi_resources() {

  // Dynamic paths can be captured with the '%' character, much like the Drupal
  // menu system.
  $items['resources/%'] = [
    'class' => 'Drupal\mymodule\MyResource',
  ];
  $items['custom/authed/resource'] = [
    'class' => 'Drupal\mymodule\OtherResource',
    'auth'  => 'Drupal\mymodule\CustomAuthenticationService',
  ];
  $items['custom/own_config/resource'] = [
    'class' => 'Drupal\mymodule\OtherResource',
    'config' => 'Drupal\mymodule\CustomConfiguration',
  ];

  return $items;

}


/**
 * Allows modification of the request before the resource responds to it. An
 * exception may be thrown in this hook to stop further processing of the
 * request.
 *
 * In order to affect a change to the request object, it must be returned from
 * this function.
 *
 * @param string $path
 *   The path of the resource being accessed.
 * @param ResourceConfiguration $resource
 *   A ResourceConfiguration object.
 * @param JsonRequest $request
 *   The request object.
 *
 * @return mixed
 *   If changes to the request need to be propagated, a modified request must be
 *   returned. If nothing is returned, changes to the request will not available
 *   downstream.
 *
 */
function hook_restapi_request($path, ResourceConfiguration $resource, JsonRequest $request) {

  // Logs statsd data on our endpoints. Note that the $path represents the
  // actual path being called, while the ResourceConfiguration::getPath() is the
  // configured path (which may contain wildcards). The latter may also differ
  // from the former in cases where internal calls are made.
  if (module_exists('statsd')) {
    statsd_call('restapi.' . $resource->getPath());
  }

}


/**
 * Allows modification of the response before it is delivered to the client. An
 * exception may be thrown in this hook to stop further processing of the
 * request.
 *
 * Note that the request object is immutable, and changes to it will not be
 * persisted.
 *
 * @param string $path
 *   The path of the resource being accessed.
 * @param ResourceConfiguration $resource
 *   A ResourceConfiguration object.
 * @param JsonRequest $request
 *   A read only copy of the request object.
 * @param ResponseInterface $response
 *   The response object.
 *
 * @return mixed
 *   If changes to the response need to be propagated, a modified request must
 *   be returned. If nothing is returned, changes to the response will not be
 *   available downstream.
 *
 */
function hook_restapi_response($path, ResourceConfiguration $resource, JsonRequest $request, ResponseInterface $response) {

  // Set a friendly message in outgoing headers.
  return $response->withHeader('X-Daily-Message', t('Have a great day!'));
}


/**
 * Allows for modification of the response depending on the exception thrown
 * while attempting to execute the request.
 *
 * If a previous module has already generated a ResponseInterface object based
 * on a specific exception, the response will be included as the second
 * parameter.
 *
 * @param Exception $e
 *   The exception that was thrown.
 * @param ResponseInterface $response
 *   A ResponseInterface object, if a previous module has already responded to
 *   this exception.
 *
 * @return ResponseInterface|NULL
 *
 */
function hook_restapi_exception(Exception $e, ResponseInterface $response = NULL) {

  if ($e instanceof RestApiException) {
    $response = JsonResponse::create("This is my modified response");
  }

  return $response;
}
