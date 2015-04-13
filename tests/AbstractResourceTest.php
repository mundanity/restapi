<?php

use Symfony\Component\HttpFoundation\Request;


/**
 * Tests for our AbstractResource class.
 *
 */
class AbstractResourceTest extends PHPUnit_Framework_TestCase {


  public function testConstructor() {

    $request = Request::createFromGlobals();
    $user    = (object) ['uid' => 1];
    $args    = [$user, $request];

    $resource = $this->getMockForAbstractClass('Drupal\restapi\AbstractResource', $args);

    $this->assertInstanceOf('Drupal\restapi\AbstractResource', $resource);

  }


  public function testToJsonReturnsResponse() {

    $request = Request::createFromGlobals();
    $user    = (object) ['uid' => 1];
    $args    = [$user, $request];

    $resource = $this->getMockForAbstractClass('Drupal\restapi\AbstractResource', $args);
    $response = $resource->toJson('testdata');

    $this->assertInstanceOf('Drupal\restapi\JsonResponse', $response);

  }


  public function testToErrorReturnsResponse() {

    $request = Request::createFromGlobals();
    $user    = (object) ['uid' => 1];
    $args    = [$user, $request];

    $resource = $this->getMockForAbstractClass('Drupal\restapi\AbstractResource', $args);
    $response = $resource->toError('testdata');

    $this->assertInstanceOf('Drupal\restapi\JsonResponse', $response);

  }

}