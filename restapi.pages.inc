<?php

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Drupal\restapi\ResourceConfiguration;


/**
 * Executes the resource based on the provided request.
 *
 * @param ResourceConfiguration $resource
 *   The resource that is being accessed, as defined in restapi_get_resources().
 * @param \StdClass $user
 *   A Drupal user object to execute the resource as.
 * @param Request $request
 *   A Symfony HTTP request object.
 * @param mixed ...
 *   Depending on the endpoint, a variable number of parameters are passed as
 *   arguments to this function.
 *
 * @return mixed
 *   A Symfony Response object, or a Drupal menu constant.
 *
 * @throws Exception
 *
 */
function restapi_page_callback(ResourceConfiguration $resource, $user, Request $request) {

  $args   = array_slice(func_get_args(), 3);
  $method = strtolower($request->getMethod());

  if (!method_exists($resource->getClass(), $method)) {
    return RESTAPI_METHOD_NOT_ALLOWED;
  }

  module_invoke_all('restapi_request', $resource, $request);

  $obj = $resource->invokeResource($user, $request);

  if (!$obj->access()) {
    return MENU_ACCESS_DENIED;
  }

  $obj->before();
  $response = call_user_func_array([$obj, $method], $args);

  if (!$response instanceof Response) {
    throw new \Exception(t("@class::@method() must return an instance of Response", [
      "@class"  => $resource->getClass(),
      "@method" => $method,
    ]));
  }

  $obj->after($response);

  module_invoke_all('restapi_response', $resource, clone $request, $response);

  return $response;

}


/**
 * Acts as a wrapper for the Drupal page callback, in order to better allow
 * internal requests to be routed.
 *
 * This function is meant to be called via hook_menu() only.
 *
 * @param ResourceConfiguration $resource
 *   The resource that is being accessed, as defined in restapi_get_resources().
 * @param mixed ...
 *   Depending on the endpoint, a variable number of parameters are passed as
 *   arguments to this function.
 *
 * @return mixed
 *   A Symfony Response object, or a Drupal menu constant.
 *
 */
function _restapi_page_callback(ResourceConfiguration $resource) {

  global $user;

  $request = restapi_get_request();
  $args    = array_slice(func_get_args(), 1);
  array_unshift($args, $resource, $user, $request);

  return call_user_func_array('restapi_page_callback', $args);

}