<?php

namespace Drupal\restapi\Auth;

use Symfony\Component\HttpFoundation\Request;


/**
 * A basic authentication class for Drupal that just checks for the "access
 * content" permission.
 *
 */
class DrupalAuthenticationService implements AuthenticationServiceInterface {

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
  public function isValid() {
    return user_access('access content', $this->user);
  }

}
