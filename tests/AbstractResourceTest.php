<?php

use Drupal\restapi\ServerRequestFactory;


/**
 * Tests for our AbstractResource class.
 *
 */
class AbstractResourceTest extends PHPUnit_Framework_TestCase {


  public function testToJsonReturnsResponse() {

    $request = ServerRequestFactory::fromGlobals();
    $user    = (object) ['uid' => 1];
    $args    = [$user, $request];

    $resource = $this->getMockForAbstractClass('Drupal\restapi\AbstractResource', $args);
    $response = $resource->toJson('testdata');

    $this->assertInstanceOf('Drupal\restapi\JsonResponse', $response);

  }


  public function testToErrorReturnsResponse() {

    $request = ServerRequestFactory::fromGlobals();
    $user    = (object) ['uid' => 1];
    $args    = [$user, $request];

    $resource = $this->getMockForAbstractClass('Drupal\restapi\AbstractResource', $args);
    $response = $resource->toError('testdata');

    $this->assertInstanceOf('Drupal\restapi\JsonResponse', $response);

  }

}