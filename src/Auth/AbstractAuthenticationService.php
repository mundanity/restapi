<?php

namespace Drupal\restapi\Auth;

use Symfony\Component\HttpFoundation\Request;


/**
 * Abstract class for authentication implementations.
 *
 */
abstract class AbstractAuthenticationService implements AuthenticationServiceInterface {

  /**
   * A HTTP Request object.
   *
   * @var Request
   */
  protected $request = NULL;


  /**
   * A Drupal user object.
   *
   * @var \StdClass
   *
   */
  protected $user = NULL;


  /**
   * {@inheritdoc}
   *
   */
  public function __construct(\StdClass $user, Request $request) {
    $this->user = $user;
    $this->request = $request;
  }


  /**
   * {@inheritdoc}
   *
   */
  abstract public function isValid();


  /**
   * Retrieves the Drupal user.
   *
   * @return \StdClass
   *
   */
  protected function getUser() {
    return $this->user;
  }


  /**
   * Returns the current request.
   *
   * @return Request
   *
   */
  protected function getRequest() {
    return $this->request;
  }

}