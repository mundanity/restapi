<?php

namespace Drupal\restapi\Auth;

use Symfony\Component\HttpFoundation\Request;


/**
 * Interface AuthenticationServiceInterface
 *
 */
interface AuthenticationServiceInterface {

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
   * Determines if the request is valid or not.
   *
   * @return boolean
   *
   */
  public function isValid();

}