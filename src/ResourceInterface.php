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
   * In case of access failure, this method must throw an appropriate
   * exception.
   *
   * @param string $method
   *   The lowercase HTTP method that is being called. (e.g. "get"). The HTTP
   *   method can be derived from the request object, but is provided here for
   *   convenience.
   *
   * @throws Exception
   *
   */
  public function access($method = 'get');


  /**
   * Handles logic before the main request is processed.
   *
   * In the case of failure, this method must throw an appropriate exception.
   *
   * @throws Exception
   *
   */
  public function before();


  /**
   * Handles logic after the main request is processed. The response can be
   * altered at this time.
   *
   * In the case of failure, this method must throw an appropriate exception.
   *
   * @param JsonResponse $response
   *   The response object after the request has been handled.
   *
   * @throws Exception
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

}