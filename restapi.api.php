<?php

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

  return $items;

}


/**
 * Allows modification of the request before the resource responds to it.
 *
 * @param Drupal\restapi\ResourceConfiguration $resource
 *   A ResourceConfiguration object.
 * @param Drupal\restapi\JsonRequest $request
 *   The request object.
 *
 */
function hook_restapi_request(
  Drupal\restapi\ResourceConfiguration $resource,
  Drupal\restapi\JsonRequest $request) {


}


/**
 * Allows modification of the response before it is delivered to the client.
 *
 * Note that the Request object is cloned, and as such, modifications to it will
 * not persist through the function call.
 *
 * @param Drupal\restapi\ResourceConfiguration $resource
 *   A ResourceConfiguration object.
 * @param Drupal\restapi\JsonRequest $request
 *   A read only copy of the request object.
 * @param Drupal\restapi\JsonResponse $response
 *   The response object.
 *
 */
function hook_restapi_response(
  Drupal\restapi\ResourceConfiguration $resource,
  Drupal\restapi\JsonRequest $request,
  Drupal\restapi\JsonResponse $response) {


}