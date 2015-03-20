<?php

namespace Drupal\restapi\Auth;


/**
 * A basic authentication class for Drupal that just checks for the "access
 * content" permission.
 *
 */
class DrupalAuthenticationService extends AbstractAuthenticationService {

  /**
   * {@inheritdoc}
   *
   */
  public function isValid() {
    return user_access('access content', $this->getUser());
  }

}
