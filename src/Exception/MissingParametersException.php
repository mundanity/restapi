<?php

namespace Drupal\restapi\Exception;


/**
 * Exception to indicate that the request is missing one or more required
 * parameters.
 *
 */
class MissingParametersException extends RestApiException {

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
    return 'missing_parameters';
  }

}