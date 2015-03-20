<?php

namespace Drupal\restapi\Auth;

use Symfony\Component\HttpFoundation\Request;


/**
 * Interface AuthenticationServiceInterface
 *
 */
interface AuthenticationServiceInterface {

  /**
   * Determines if the request is valid or not.
   *
   * @return boolean
   *
   */
  public function isValid();

}