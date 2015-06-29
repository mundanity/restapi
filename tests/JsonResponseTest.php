<?php

use Drupal\restapi\JsonResponse;


class JsonResponseTest extends PHPUnit_Framework_TestCase {

  /**
   * Ensures that our static constructor works.
   *
   */
  public function testCreate() {

    $data = [
      'foo' => 'foo',
      'bar' => 'baz',
    ];
    $headers = [
      'x-test' => 'testing',
    ];
    $expected = json_encode($data, JSON_PRETTY_PRINT);
    $response = JsonResponse::create($data, 205, $headers);

    $this->assertEquals(205, $response->getStatusCode());
    $this->assertEquals('testing', $response->getHeaderLine('x-test'));
    $this->assertEquals($expected, (string) $response->getBody());
  }


  /**
   * Ensures that getData() returns data as expected.
   *
   */
  public function testGetData() {

    $data = [
      'foo' => 'foo',
      'bar' => 'baz',
    ];

    $response = JsonResponse::create($data);
    $result   = $response->getData();

    $this->assertArrayHasKey('foo', $result);
    $this->assertArrayHasKey('bar', $result);
    $this->assertEquals($result['foo'], 'foo');
    $this->assertEquals($result['bar'], 'baz');
  }

}