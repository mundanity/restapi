<?php

namespace Drupal\restapi;

use Psr\Http\Message\ServerRequestInterface;
use Exception;


/**
 * A Resource represents an endpoint that can react to various HTTP methods.
 *
 * Implementing classes should add one or more methods that correspond to the
 * HTTP method desired. (e.g. MyResource::get() to response to a GET request).
 * These methods must return a HTTP Request object.
 *
 *
 *
 */
interface ResourceInterface {

  /**
   * Constructor
   *
   * @param \StdClass $user
   *   A Drupal user object.
   * @param ServerRequestInterface $request
   *   A HTTP request.
   *
   */
  public function __construct(\StdClass $user, ServerRequestInterface $request);


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
   * @param string $method
   *   The lowercase HTTP method that is being called. (e.g. "get"). The HTTP
   *   method can be derived from the request object, but is provided here for
   *   convenience.
   *
   */
  public function access($method = 'get');


  /**
   * A more specific access call will be called for the current HTTP method.
   * e.g. accessGet(), accessPost(), accessPut(), etc.
   *
   */
  // public function accessGet();
  // public function accessPost();
  // public function accessPut();
  // public function accessPatch();
  // public function accessDelete();
  // public function accessHead();
  // public function accessOptions();


  /**
   * Handles logic before the main request is processed.
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
   * @param JsonResponse $response
   *   The response object after the request has been handled.
   *
   */
  public function after(JsonResponse $response);


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