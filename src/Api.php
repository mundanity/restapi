<?php

namespace Drupal\restapi;

use Drupal\restapi\Exception\InvalidParametersException;
use Drupal\restapi\Exception\MissingParametersException;
use Drupal\restapi\Exception\RestApiException;
use Drupal\restapi\Exception\UnauthorizedException;
use Exception;
use Psr\Http\Message\ResponseInterface;


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

    $request = $this->getRequest()
      ->withMethod($method)
      ->withData($data);

    // Set headers on the new request object.
    foreach ($headers as $key => $value) {
      $request = $request->withHeader($key, $value);
    }

    // If we are dealing with a json request, then a version must also be sent.
    if (strpos($request->getHeaderLine('accept'), 'application/json') !== FALSE && !$request->getVersion()) {
      return $this->toError(t('Missing required API version number.'), 'missing_version', 400);
    }

    $versioned_method = $this->getVersionedMethodFromResource($resource, $method, $request);

    if (!$versioned_method) {
      $message = sprintf('The method "%s" is not available for the resource "%s".', $method, $path);
      return $this->toError($message, 'not_allowed', 405);
    }

    // Sets the new path, if it is different.
    $uri = $request->getUri();

    if ($path != $uri->getPath()) {
      $uri = $uri->withPath($path);
      $request = $request->withUri($uri);
    }

    try {
      $request = $this->invokeHookRequest($path, $resource, $request);

      $obj  = $resource->invokeResource($this->getUser(), $request);
      $args = $resource->getArgumentsForPath($path);
      $obj->before();

      $this->handleRequiredParameters($obj, $request);

      $this->handleAccess($obj, $resource, $method, $request, $args);
      $response = call_user_func_array([$obj, $versioned_method], $args);

      if (!$response instanceof ResponseInterface) {
        $message = sprintf('%s::%s() must return an instance of ResponseInterface.', $resource->getClass(), $method);
        throw new Exception($message);
      }

      $obj->after($response);
    }
    catch (Exception $e) {

      $response = restapi_invoke_hook_exception($e);

      if (!$response) {
        $response = $this->toError($e->getMessage());
      }
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
   * Handles required parameters.
   *
   * @param ResourceInterface $resource
   *   The resource being called.
   * @param JsonRequest $request
   *   The HTTP request.
   *
   * @throws MissingParametersException
   * @throws InvalidParametersException
   *
   */
  protected function handleRequiredParameters(ResourceInterface $resource, JsonRequest $request) {

    $required = $resource->getRequiredParameters();
    $params   = strtoupper($request->getMethod()) == 'GET' ? $request->getQueryParams() : $request->getParsedBody();
    $invalid  = [];
    $missing  = [];

    foreach($required as $param => $callable) {
      if (empty($params[$param])) {
        $missing[] = $param;
        continue;
      }

      if (is_callable($callable) && !$callable($params[$param])) {
        $invalid[] = $param;
      }
    }

    if ($missing) {
      $message = sprintf('Missing required parameter(s): %s', implode(', ', $missing));
      throw new MissingParametersException($message, 400);
    }

    if ($invalid) {
      $message = sprintf('Invalid values for parameter(s): %s', implode(', ', $invalid));
      throw new InvalidParametersException($message, 400);
    }
  }


  /**
   * Handles access for the resource being called.
   *
   * @param ResourceInterface $resource
   *   The resource being called.
   * @param ResourceConfigurationInterface $resource_config
   *   The resource configuration.
   * @param string $method
   *   The HTTP method of the request.
   * @param JsonRequest $request
   *   The request.
   * @param array $args
   *   An array of arguments derived from the URL.
   *
   * @throws RestApiException
   * @throws UnauthorizedException
   *
   */
  protected function handleAccess(ResourceInterface $resource, ResourceConfigurationInterface $resource_config, $method, JsonRequest $request, array $args = []) {

    if (method_exists($resource, 'access')) {
      $result = call_user_func_array([$resource, 'access'], $args);

      if ($result === FALSE) {
        throw new UnauthorizedException('You do not have permission to access this resource.');
      }

      if ($result instanceof JsonResponse) {
        $body = json_decode((string) $result->getBody(), TRUE);
        throw new RestApiException($body['message'], $result->getStatusCode(), NULL, $body['error']);
      }
    }

    $method_name = 'access' . ucfirst($method);
    $access = $this->getVersionedMethodFromResource($resource_config, $method_name, $request);

    if ($access) {
      $result = call_user_func_array([$resource, $access], $args);

      if ($result === FALSE) {
        throw new UnauthorizedException('You do not have permission to access this resource.');
      }

      if ($result instanceof JsonResponse) {
        $body = json_decode((string) $result->getBody(), TRUE);
        throw new RestApiException($body['message'], $result->getStatusCode(), NULL, $body['error']);
      }
    }
  }


  /**
   * Helper method to invoke hook_restapi_response.
   *
   * @param string $path
   *   The path.
   * @param ResourceConfigurationInterface $resource
   *   The resource configuration.
   * @param JsonRequest $request
   *   The HTTP request.
   *
   * @return JsonRequest
   *
   */
  protected function invokeHookRequest($path, ResourceConfigurationInterface $resource, JsonRequest $request) {

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
   * @param ResourceConfigurationInterface $resource
   *   The resource configuration.
   * @param JsonRequest $request
   *   The HTTP request.
   * @param ResponseInterface $response
   *   The HTTP response.
   *
   * @return JsonResponse
   *
   */
  protected function invokeHookResponse($path, ResourceConfigurationInterface $resource, JsonRequest $request, ResponseInterface $response) {

    foreach(module_implements('restapi_response') as $module) {
      $func   = $module . '_restapi_response';
      $result = $func($path, $resource, $request, $response);

      if ($result instanceof ResponseInterface) {
        $response = $result;
      }
    }

    return $response;
  }


  /**
   * Helper method to determine which method is available. The version will
   * decrement/cascade down from the specified version all the way down to a
   * non-versioned method.
   *
   * @param ResourceConfigurationInterface $resource
   *   The resource configuration.
   * @param $method
   *   The method name that we are trying to call.
   * @param JsonRequest $request
   *   The request being made.
   *
   * @return string|NULL
   *   Returns the name of the method to call, if one can be found, or null if
   *   no method exists that can satisfy the request.
   *
   */
  protected function getVersionedMethodFromResource(ResourceConfigurationInterface $resource, $method, JsonRequest $request) {

    $current_version = variable_get('restapi_current_version', 1);
    $version         = $current_version > $request->getVersion() ?
      $request->getVersion() : $current_version;

    for (; $version > 0; $version--) {
      $method_name = $method . 'V' . $version;

      if (method_exists($resource->getClass(), $method_name)) {
        return $method_name;
      }
    }

    return method_exists($resource->getClass(), $method) ? $method : NULL;
  }

}
