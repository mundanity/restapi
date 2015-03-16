<?php

use Drupal\restapi\Auth\DrupalAuthenticationService;


/**
 * Tests our AuthenticationServiceInterface
 *
 */
class AuthenticationServiceInterfaceTest extends PHPUnit_Framework_TestCase {

  public function testConstructor() {

    $user = (object) ['uid' => 5, 'name' => 'testuser'];
    $request = $this->getMock('Symfony\Component\HttpFoundation\Request');

    $auth = new DrupalAuthenticationService($user, $request);
    $this->assertInstanceOf('Drupal\restapi\Auth\AuthenticationServiceInterface', $auth);

  }

}