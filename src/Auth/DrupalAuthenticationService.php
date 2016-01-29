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
  public function authenticate() {
    if (!user_is_logged_in()) {
      return FALSE;
    }

    if (!user_access('access content', $this->getUser())) {
      return FALSE;
    }

    return $this->getUser();
  }

}
