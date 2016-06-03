<?php

namespace Drupal\restapi;


/**
 * A factory of sorts, providing methods to generate an appropriate restapi HTTP
 * response.
 *
 * @todo This should likely be an interface, which then can have a JSON / XML /
 * whatever subclass. This does involve injecting the HttpResponseFactory in
 * the AbstractResource a bit better, and also removing / dealing with the
 * toJson() method in the class.
 *
 */
class HttpResponseFactory {

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
  public function toJson($data, $status = 200) {
    return JsonResponse::create($data, $status);
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
    return $this->toJson($data, $status);
  }


  /**
   * Helper method to return a 400 error response.
   *
   * @param string $message
   *   The optional error message.
   *
   * @return JsonResponse
   *
   */
  public function to400($message = NULL) {

    $message = $message ?: 'Validation failed';
    return $this->toError($message, 'validation_failed', 400);
  }


  /**
   * Helper method to return a 403 error response.
   *
   * @param string $message
   *   The optional error message.
   *
   * @return JsonResponse
   *
   */
  public function to403($message = NULL) {

    $message = $message ?: 'Permission denied';
    return $this->toError($message, 'forbidden', 403);
  }


  /**
   * Helper method to return a 404 error response.
   *
   * @param string $message
   *   The optional error message.
   *
   * @return JsonResponse
   *
   */
  public function to404($message = NULL) {

    $message = $message ?: 'Resource not found';
    return $this->toError($message, 'not_found', 404);
  }

}
