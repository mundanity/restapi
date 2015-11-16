<?php

use Drupal\restapi\HttpResponseFactory;


/**
 * Tests for a HttpResponseFactory.
 *
 */
class HttpResponseFactoryTest extends PHPUnit_Framework_TestCase {

  /**
   * Ensures that toJson() works as expected.
   *
   */
  public function testToJson() {

    $factory  = new HttpResponseFactory();
    $response = $factory->toJson('message');

    $this->assertInstanceOf('Drupal\restapi\JsonResponse', $response);
    $this->assertEquals(200, $response->getStatusCode());
    $this->assertEquals('"message"', (string) $response->getBody());
  }


  /**
   * Ensures that toJson() can change the HTTP status code.
   *
   */
  public function testToJsonStatusCodeUpdate() {

    $factory  = new HttpResponseFactory();
    $response = $factory->toJson('message', 201);

    $this->assertEquals(201, $response->getStatusCode());
  }


  /**
   * Ensures that toJson() can accept an array as the data parameter.
   *
   */
  public function testToJsonAcceptsArray() {

    $data     = ['data' => 'true'];
    $factory  = new HttpResponseFactory();
    $response = $factory->toJson($data);

    $this->assertEquals(json_encode($data, JSON_PRETTY_PRINT), (string) $response->getBody());
  }


  /**
   * Ensures that to400() works as expected.
   *
   */
  public function testTo400() {

    $factory  = new HttpResponseFactory();
    $response = $factory->to400('error');

    $this->assertEquals(400, $response->getStatusCode());
  }


  /**
   * Ensures that to403() works as expected.
   *
   */
  public function testTo403() {

    $factory  = new HttpResponseFactory();
    $response = $factory->to403('error');

    $this->assertEquals(403, $response->getStatusCode());
  }


  /**
   * Ensures that to404() works as expected.
   *
   */
  public function testTo404() {

    $factory  = new HttpResponseFactory();
    $response = $factory->to404('error');

    $this->assertEquals(404, $response->getStatusCode());
  }


  /**
   * Ensures that toError() works as expected.
   *
   */
  public function testToError() {

    $factory  = new HttpResponseFactory();
    $response = $factory->toError('error', 'code', 501);

    $expected = json_encode([
      'error'   => 'code',
      'message' => 'error',
    ], JSON_PRETTY_PRINT);

    $this->assertEquals($expected, (string) $response->getBody());
    $this->assertEquals(501, $response->getStatusCode());
  }
}
