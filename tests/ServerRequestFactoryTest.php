<?php

use Drupal\restapi\ServerRequestFactory;
use Zend\Diactoros\Stream;


/**
 * Tests for the ServerRequestFactory class.
 *
 */
class ServerRequestFactoryTest extends PHPUnit_Framework_TestCase {

  /**
   * Ensures that fromGlobals() returns a JsonRequest.
   *
   */
  public function testFromGlobalsReturnsJsonRequest() {

    $request = ServerRequestFactory::fromGlobals();
    $this->assertInstanceOf('Drupal\restapi\JsonRequest', $request);
  }


  /**
   * Ensures that passing an array to fromGlobals() results in
   * JsonRequest::parsedBody() being set as expected.
   *
   */
  public function testBodyArrayIsSetAsParsedData() {

    $body = [
      'foo' => 'foo',
      'bar' => 'baz',
    ];

    $request = ServerRequestFactory::fromGlobals(null, null, $body, null, null);
    $this->assertEquals($body, $request->getParsedBody());
  }


  /**
   * Ensures that fromGlobals() results in JsonRequest::parsedBody() being set
   * when given a JSON string as PHP input.
   *
   */
  public function testJsonStringIsSetAsParsedData() {

    $server = [
      'HTTP_ACCEPT' => 'application/json',
      'REQUEST_METHOD' => 'POST',
    ];

    $content = new Stream('data://text/plain,{"test":"testing"}');
    $expected = [
      'test' => 'testing',
    ];

    $request = ServerRequestFactory::fromGlobals($server, null, null, null, null, $content);
    $this->assertEquals($expected, $request->getParsedBody());
  }


  /**
   * Ensures that fromGlobals does not set the parsed body when the HTTP accept
   * header does not contain application/json.
   *
   */
  public function testNoParsedDataIfAcceptDoesNotContainApplicationJson() {

    $server =[
      'REQUEST_METHOD' => 'POST',
    ];

    $content = new Stream('data://text/plain,{"test":"testing"}');
    $request = ServerRequestFactory::fromGlobals($server, null, null, null, null, $content);

    $this->assertEquals([], $request->getParsedBody());

  }


  /**
   * Ensures that fromGlobals does not set the parsed body when the HTTP method
   * is GET.
   *
   */
  public function testNoParsedDataIfHttpMethodIsGet() {

    $server =[
      'HTTP_ACCEPT' => 'application/json',
    ];

    $content = new Stream('data://text/plain,{"test":"testing"}');
    $request = ServerRequestFactory::fromGlobals($server, null, null, null, null, $content);

    $this->assertEquals([], $request->getParsedBody());

  }
}