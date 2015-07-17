<?php

namespace Drupal\restapi;

use Drupal\restapi\Exception\RestApiException;
use Drupal\restapi\Exception\UnauthorizedException;
use Exception;


/**
 * The API class. Allows for manipulation of the endpoints.
 *
 */
class Api {

  /**
   * The Drupal user to call this resource as.
   *
   * @var \StdClass
   *
   */
  protected $user = NULL;

  /**
   * The request to use for this resource.
   *
   * @var JsonRequest
   *
   */
  protected $request = NULL;


  /**
   * Constructor
   *
   * @param \StdClass $user
   *   The Drupal user to call this resource as. Defaults to the current user.
   * @param JsonRequest $request
   *   The request to use to set the context for the resource. Defaults to the
   *   current page request.
   *
   */
  public function __construct(\StdClass $user = NULL, JsonRequest $request = NULL) {

    if (!$request) {
      $request = ServerRequestFactory::fromGlobals();
    }

    $this->request = $request;
    $this->user = $user ?: $GLOBALS['user'];
  }


  /**
   * Overrides the current set user.
   *
   * @param \StdClass $user
   *   The Drupal user to set for the API.
   *
   */
  public function setUser(\StdClass $user) {
    $this->user = $user;
  }


  /**
   * Overrides the current set request.
   *
   * @param JsonRequest $request
   *   The request for the API.
   *
   */
  public function setRequest(JsonRequest $request) {
    $this->request = $request;
  }


  /**
   * Returns the current user this API call is acting as.
   *
   * @return \StdClass
   *
   */
  public function getUser() {
    return $this->user;
  }


  /**
   * Returns the currently set request.
   *
   * @return JsonRequest
   *
   */
  public function getRequest() {
    return $this->request;
  }


  /**
   * Executes an API resource.
   *
   * @param string $method
   *   The HTTP method to call. This will override the method in the current
   *   Request object.
   * @param string $path
   *   The path to the resource. This will override the path in the current
   *   Request object.
   * @param array $data
   *   (optional) An array of data to provide to the resource. Any data provided
   *   will override the values in the current Request object.
   * @param array $headers
   *   (optional) An array of headers to provide to the resource.
   *
   * @return JsonResponse
   *
   */
  public function call($method, $path, array $data = [], array $headers = []) {

    $resource = restapi_get_resource($path);
    $method   = strtolower($method);

    if (!$resource) {
      $message = sprintf('The path "%s" does not match any known resources.', $path);
      return $this->toError($message, 'not_found', 404);
    }

    if (!method_exists($resource->getClass(), $method)) {
      $message = sprintf('The method "%s" is not available for the resource "%s".', $method, $path);
      return $this->toError($message, 'not_allowed', 405);
    }

    $request = $this->getRequest()
      ->withMethod($method)
      ->withData($data);

    // Sets the new path, if it is different.
    $uri = $request->getUri();

    if ($path != $uri->getPath()) {
      $uri = $uri->withPath($path);
      $request = $request->withUri($uri);
    }

    // Set headers on the new request object.
    foreach ($headers as $key => $value) {
      $request = $request->withHeader($key, $value);
    }

    try {
      $request = $this->invokeHookRequest($path, $resource, $request);

      $obj = $resource->invokeResource($this->getUser(), $request);
      $this->handleAccess($obj, $method);
      $obj->before();

      $args = $resource->getArgumentsForPath($path);
      $response = call_user_func_array([$obj, $method], $args);

      if (!$response instanceof JsonResponse) {
        $message = sprintf('%s::%s() must return an instance of JsonResponse.', $resource->getClass(), $method);
        throw new Exception($message);
      }

      $obj->after($response);
    }
    catch (RestApiException $e) {
      $response = $this->toError($e->getMessage(), (string) $e, $e->getCode());
    }
    catch (Exception $e) {
      $response = $this->toError($e->getMessage());
    }

    $response = $this->invokeHookResponse($path, $resource, $request, $response);
    return $response;
  }


  /**
   * Helper method to return a JSON error response.
   *
   * @param string $message
   *   The error message.
   * @param string $code
   *   The error code to include in the response. This is typically a machine
   *   name representation of the error. (e.g. "unauthorized" or "not_allowed")
   * @param int $status
   *   The HTTP status code for this response.
   *
   * @return JsonResponse
   *
   */
  public function toError($message, $code = 'system', $status = 500) {
    $data = [
      'error'   => $code,
      'message' => $message,
    ];
    return JsonResponse::create($data, $status);
  }


  /**
   * Handles access for the resource being called.
   *
   * @param ResourceInterface $resource
   *   The resource being called.
   * @param string $method
   *   The HTTP method of the request.
   *
   * @throws RestApiException
   * @throws UnauthorizedException
   *
   */
  protected function handleAccess(ResourceInterface $resource, $method) {

    $result = $resource->access();

    if ($result === FALSE) {
      throw new UnauthorizedException('Permission denied');
    }

    if ($result instanceof JsonResponse) {
      throw new RestApiException((string) $result->getBody(), $result->getStatusCode());
    }

    $access = 'access' . ucfirst($method);

    if (method_exists($resource, $access)) {
      $result = $resource->$access();

      if ($result === FALSE) {
        throw new UnauthorizedException('Permission denied');
      }

      if ($result instanceof JsonResponse) {
        throw new RestApiException((string) $result->getBody(), $result->getStatusCode());
      }
    }
  }


  /**
   * Helper method to invoke hook_restapi_response.
   *
   * @param string $path
   *   The path.
   * @param ResourceConfiguration $resource
   *   The resource configuration.
   * @param JsonRequest $request
   *   The HTTP request.
   *
   * @return JsonRequest
   *
   */
  protected function invokeHookRequest($path, ResourceConfiguration $resource, JsonRequest $request) {

    foreach(module_implements('restapi_request') as $module) {
      $func   = $module . '_restapi_request';
      $result = $func($path, $resource, $request);

      if ($result instanceof JsonRequest) {
        $request = $result;
      }
    }

    return $request;
  }


  /**
   * Helper method to invoke hook_restapi_response.
   *
   * @param string $path
   *   The path.
   * @param ResourceConfiguration $resource
   *   The resource configuration.
   * @param JsonRequest $request
   *   The HTTP request.
   * @param JsonResponse $response
   *   The HTTP response.
   *
   * @return JsonResponse
   *
   */
  protected function invokeHookResponse($path, ResourceConfiguration $resource, JsonRequest $request, JsonResponse $response) {

    foreach(module_implements('restapi_response') as $module) {
      $func = $module . '_restapi_response';
      $result = $func($path, $resource, $request, $response);

      if ($result instanceof JsonResponse) {
        $response = $result;
      }
    }

    return $response;
  }

}