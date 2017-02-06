<?php

namespace Drupal\restapi;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;


/**
 * A Resource represents an endpoint that can react to various HTTP methods.
 *
 * Implementing classes should add one or more methods that correspond to the
 * HTTP method desired. (e.g. MyResource::get() to response to a GET request).
 * These methods must return a HTTP Request object.
 *
 */
interface ResourceInterface {

  /**
   * Determines whether this resource can be accessed by the current user /
   * request.
   *
   * In case of access failure, this method can either return FALSE, or a
   * JsonResponse with more specific information. All other returned values
   * (including NULL) will be assumed as success.
   *
   * This method is called before the HTTP method specific version. e.g.
   * Resource::access() will be called before Resource::accessGet().
   *
   * Arguments from the URL will be passed to the method.
   *
   * <code>
   *   public function access();
   * </code>
   *
   */


  /**
   * A more specific access call will be called for the current HTTP method.
   * e.g. accessGet(), accessPost(), accessPut(), etc.
   *
   * Arguments from the URL will be passed as parameters to the method.
   *
   * <code>
   *   public function accessGet();
   *   public function accessPost();
   *   public function accessPut();
   *   public function accessPatch();
   *   public function accessDelete();
   *   public function accessHead();
   *   public function accessOptions();
   * </code>
   *
   */


  /**
   * Handles logic before the main request is processed. This is a good place to
   * add any required parameters.
   *
   */
  public function before();


  /**
   * Handles logic after the main request is processed. The response may NOT be
   * altered at this time.
   *
   * Note that this method will not be called if the main method, e.g. get() or
   * post(), does NOT return a JsonResponse object.
   *
   * @param ResponseInterface $response
   *   The response object after the request has been handled.
   *
   */
  public function after(ResponseInterface $response);


  /**
   * Signifies that a parameter is required. For GET requests, parameters will
   * be looked for in the query string. For all others, parameters will be
   * looked for within the request body.
   *
   * If the parameter is not available, the request will return a HTTP 400
   * error.
   *
   * Required parameters are evaluated BEFORE ResourceInterface::access() and
   * ResourceInterface::access{METHOD} are called, and can be defined in
   * ResourceInterface::before(), or in a custom constructor.
   *
   * @param string $name
   *   The name of the required parameter.
   * @param callable $validator
   *   An optional callable that can additionally validate the parameter. The
   *   parameter's value will be passed to the validator. The validator MUST
   *   return either true (the value is valid) or false (the value is invalid).
   *
   */
  public function requireParameter($name, callable $validator = NULL);


  /**
   * Returns an array of required parameters.
   *
   * The key of the array is the required parameter's name, while the value is
   * the optional callable (or TRUE, if no callable was declared).
   *
   * @return array
   *
   */
  public function getRequiredParameters();


  /**
   * Helper method to return a JSON response.
   *
   * @param mixed $data
   *  The data to return as the response.
   * @param int $status
   *  The HTTP status code for this response.
   *
   * @return JsonResponse
   *
   * @deprecated To be replaced with a more generic toResponse() or similar.
   *
   */
  public function toJson($data, $status = 200);


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
  public function toError($message, $code = 'system', $status = 500);


  /**
   * Helper method to return a 403 error response.
   *
   * @param string $message
   *   The optional error message.
   *
   * @return JsonResponse
   *
   */
  public function to403($message = NULL);


  /**
   * Helper method to return a 404 error response.
   *
   * @param string $message
   *   The optional error message.
   *
   * @return JsonResponse
   *
   */
  public function to404($message = NULL);

}
