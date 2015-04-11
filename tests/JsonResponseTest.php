<?php

use Drupal\restapi\JsonResponse;


class JsonResponseTest extends PHPUnit_Framework_TestCase {

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