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
   * The code should be set to a valid HTTP status code.
   *
   */
  // protected $code = 404;


  /**
   * The exception should override __toString().
   *
   */
  // public function __toString() {
  //   return 'short_name';
  // }

}