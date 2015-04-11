<?php

namespace Drupal\restapi\Exception;


/**
 * Exception to indicate that the user cannot access the current resource.
 *
 * While the NotFoundException should be thrown in cases where the current user
 * may or may not know about the resource being accessed, this can be thrown in
 * situations where the current user already knows about the resource, but still
 * does not have access to it.
 *
 */
class UnauthorizedException extends RestApiException {

  /**
   * The exception code.
   *
   * @var int
   *
   */
  protected $code = 403;


  /**
   * {@inheritdoc}
   *
   */
  public function __toString() {
    return 'unauthorized';
  }

}