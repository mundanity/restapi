<?php

namespace Drupal\restapi\Exception;


/**
 * Exception to indicate that the requested resource does not exist, or that the
 * user may not have access to it.
 *
 * This ambiguity is to prevent unauthorized users from knowing that a resource
 * might exist, even if they may never have access to it.
 *
 */
class NotFoundException extends RestApiException {

  /**
   * The exception code.
   *
   * @var int
   *
   */
  protected $code = 404;


  /**
   * {@inheritdoc}
   *
   */
  public function __toString() {
    return 'not_found';
  }

}