<?php

namespace Drupal\restapi\Exception;


/**
 * Exception to indicate that one or more parameters for the request are
 * invalid.
 *
 */
class InvalidParametersException extends RestApiException {

  /**
   * The exception code.
   *
   * @var int
   *
   */
  protected $code = 400;


  /**
   * {@inheritdoc}
   *
   */
  public function __toString() {
    return 'invalid_parameters';
  }

}