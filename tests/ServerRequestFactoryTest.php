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
   * correctly when given a non-scalar JSON string as PHP input.
   *
   */
  public function testNonScalarJsonStringIsSetAsParsedData() {

    $server = [
      'CONTENT_TYPE' => 'application/json',
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
   * Ensures that fromGlobals() results in JsonRequest::parsedBoyd() being set
   * correctly when given a scalar JSON string as PHP input.
   *
   */
  public function testScalarJsonStringIsSetAsParsedData() {

    $server = [
      'CONTENT_TYPE' => 'application/json',
      'REQUEST_METHOD' => 'POST',
    ];

    $content = new Stream('data://text/plain,null');
    $expected = [];

    $request = ServerRequestFactory::fromGlobals($server, null, null, null, null, $content);
    $this->assertEquals($expected, $request->getParsedBody());


    $content = new Stream('data://text/plain,"test"');
    $expected = ['test'];

    $request = ServerRequestFactory::fromGlobals($server, null, null, null, null, $content);
    $this->assertEquals($expected, $request->getParsedBody());
  }


  /**
   * Ensures that fromGlobals does not set the parsed body when the HTTP content
   * type header is not application/json.
   *
   */
  public function testNoParsedDataIfContentTypeIsNotApplicationJson() {

    $server =[
      'REQUEST_METHOD' => 'POST',
    ];

    $content = new Stream('data://text/plain,{"test":"testing"}');
    $request = ServerRequestFactory::fromGlobals($server, null, null, null, null, $content);

    $this->assertEquals(['{"test":"testing"}' => ''], $request->getParsedBody());

  }


  /**
   * Ensures that fromGlobals does not set the parsed body when the HTTP method
   * is GET.
   *
   */
  public function testNoParsedDataIfHttpMethodIsGet() {

    $server =[
      'CONTENT_TYPE' => 'application/json',
    ];

    $content = new Stream('data://text/plain,{"test":"testing"}');
    $request = ServerRequestFactory::fromGlobals($server, null, null, null, null, $content);

    $this->assertEquals([], $request->getParsedBody());

  }
}