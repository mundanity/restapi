<?php

namespace Drupal\restapi\Auth;

use Psr\Http\Message\RequestInterface;


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
   * @param RequestInterface $request
   *   A HTTP Request object.
   *
   */
  public function __construct(\StdClass $user, RequestInterface $request);


  /**
   * Determines if the request is valid or not.
   *
   * @return boolean
   *
   */
  public function isValid();

}