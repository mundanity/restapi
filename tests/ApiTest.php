<?php

use Drupal\restapi\Api;


class ApiTest extends PHPUnit_Framework_TestCase {


  public function setUp() {

    $this->request = $this->createMock('Drupal\restapi\JsonRequest');
    $this->user = (object) [
      'uid' => 1,
      'name' => 'user',
    ];

    $this->api = new Api($this->user, $this->request);
  }


  public function testGetters() {
    $this->assertSame($this->request, $this->api->getRequest());
    $this->assertSame($this->user, $this->api->getUser());
  }


  public function testSetters() {

    $user = (object) [
      'uid'  => 2,
      'name' => 'new user',
    ];
    $request = $this->createMock('Drupal\restapi\JsonRequest');

    $this->api->setUser($user);
    $this->api->setRequest($request);

    $this->assertSame($user, $this->api->getUser());
    $this->assertSame($request, $this->api->getRequest());

  }


  public function testToErrorReturnsResponse() {
    $response = $this->api->toError('testdata');
    $this->assertInstanceOf('Drupal\restapi\JsonResponse', $response);
  }

}
