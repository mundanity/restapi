<?php

namespace Drupal\restapi\Exception;

use Exception;


/**
 * A Rest API exception. Rest API exceptions must have the following properties:
 *
 * - The exception code must be a valid HTTP status code. (e.g. 404 for a
 *   not found exception.)
 * - The implementation of __toString() should return the machine name of the
 *   error. (e.g. "not_found" for a not found exception.
 * - Custom errors thrown from a resource must extend this class.
 *
 */
class RestApiException extends Exception {

  /**
   * Constructor
   *
   * @param string $message
   *   The message.
   * @param int $code
   *   The HTTP error code.
   * @param Exception $previous
   *   The parent exception, if relevant.
   * @param string $short_message
   *   The machine readable category.
   *
   */
  public function __construct($message, $code = 0, Exception $previous = NULL, $short_message = 'system') {
    parent::__construct($message, $code, $previous);
    $this->short_message = $short_message;
  }


  /**
   * The code should be set to a valid HTTP status code.
   *
   */
  // protected $code = 404;


  /**
   * Returns a machine readable "category" for the exception.
   *
   */
  public function __toString() {
    return $this->short_message;
  }
}