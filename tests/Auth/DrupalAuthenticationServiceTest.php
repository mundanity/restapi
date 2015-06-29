<?php

use Drupal\restapi\Auth\DrupalAuthenticationService;


/**
 * Tests our AuthenticationServiceInterface
 *
 */
class AuthenticationServiceInterfaceTest extends PHPUnit_Framework_TestCase {

  /**
   * Ensures that isValid() executes as expected.
   *
   */
  public function testIsValid() {



  }

}


function user_access($permission, $user) {
  if (!empty($user->fail_access_check)) {
    return FALSE;
  }
  return TRUE;
}