<?php

namespace Drupal\restapi;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;


/**
 * A Resource represents an endpoint that can react to various HTTP methods.
 *
 * Implementing classes should add one or more methods that correspond to the
 * HTTP method desired. (e.g. MyResource::get() to response to a GET request).
 * These methods must return a Symfony Response object.
 *
 */
interface ResourceInterface {

  /**
   * Constructor
   *
   * @param \StdClass $user
   *   A Drupal user object.
   * @param Request $request
   *   A Symfony HTTP Request object.
   *
   */
  public function __construct(\StdClass $user, Request $request);


  /**
   * Determines whether this resource can be accessed by the current user /
   * request.
   *
   * @param string $method
   *   The lowercased HTTP method that is being called. (e.g. "get").
   *
   * @return boolean
   *
   */
  public function access($method = 'get');


  /**
   * Handles logic before the main request is processed.
   *
   * @return void
   *
   */
  public function before();


  /**
   * Handles logic after the main request is processed. The response can be
   * altered at this time.
   *
   * @param Response $response
   *
   * @return void
   *
   */
  public function after(Response $response);


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

}