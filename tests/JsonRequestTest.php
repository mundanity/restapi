<?php

use Drupal\restapi\ServerRequestFactory;


/**
 * Tests for a JsonRequest class.
 *
 */
class JsonRequestTest extends PHPUnit_Framework_TestCase {

  /**
   * Ensures that get() works as expected for GET requests.
   *
   */
  public function testGetWithHttpGet() {

    $server['REQUEST_METHOD'] = 'GET';

    $request = ServerRequestFactory::fromGlobals($server, ['var' => 'set']);
    $this->assertEquals('set', $request->get('var'));
  }


  /**
   * Ensures that get() ignores a query param if using making a non-GET HTTP
   * request.
   *
   */
  public function testGetReturnsNullWithNonGetRequest() {

    $server['REQUEST_METHOD'] = 'POST';

    $request = ServerRequestFactory::fromGlobals($server, ['var' => 'set']);
    $this->assertNull($request->get('var'));
  }


  /**
   * Ensures that get() works as expected for PUT, POST, DELETE, and PATCH
   * requests.
   *
   */
  public function testPutPostDeleteAndPatchSetReturnsForGet() {

    $server['REQUEST_METHOD'] = 'POST';

    $request = ServerRequestFactory::fromGlobals($server, null, ['var' => 'set']);
    $this->assertEquals('set', $request->get('var'));

    $server['REQUEST_METHOD'] = 'PUT';

    $request = ServerRequestFactory::fromGlobals($server, null, ['var' => 'set']);
    $this->assertEquals('set', $request->get('var'));

    $server['REQUEST_METHOD'] = 'PATCH';

    $request = ServerRequestFactory::fromGlobals($server, null, ['var' => 'set']);
    $this->assertEquals('set', $request->get('var'));

    $server['REQUEST_METHOD'] = 'DELETE';

    $request = ServerRequestFactory::fromGlobals($server, null, ['var' => 'set']);
    $this->assertEquals('set', $request->get('var'));
  }


  /**
   * Ensures that get() ignores parsed body variables when handling GET
   * requests.
   *
   */
  public function testGetIgnoresParsedBodyWhenUnderHttpGet() {

    $request = ServerRequestFactory::fromGlobals(null, null, ['var' => 'set']);
    $this->assertNull($request->get('var'));
  }


  /**
   * Ensures that withData() works as expected with GET requests.
   *
   */
  public function testWithDataWithGet() {

    $data = [
      'test' => 'testing',
    ];

    $request = ServerRequestFactory::fromGlobals();
    $request = $request->withData($data);

    $this->assertEquals($data, $request->getQueryParams());
    $this->assertEquals([], $request->getParsedBody());
  }


  /**
   * Ensures that withData() works as expected with POST requests.
   *
   */
  public function testWithDataWithPost() {

    $data = [
      'test' => 'testing',
    ];

    $request = ServerRequestFactory::fromGlobals(['REQUEST_METHOD' => 'POST']);
    $request = $request->withData($data);

    $this->assertEquals([], $request->getQueryParams());
    $this->assertEquals($data, $request->getParsedBody());
  }


  /**
   * Ensures that getVersion() works as expected.
   *
   */
  public function testGetVersion() {

    $server['HTTP_ACCEPT'] = 'application/json; version=3';

    $request = ServerRequestFactory::fromGlobals($server);
    $this->assertEquals(3, $request->getVersion());
  }


  /**
   * Ensures that only the first version found is used.
   *
   */
  public function testFirstVersionHeaderIsUsed() {

    $server['HTTP_ACCEPT'] = 'application/json; version=2, application/json; version=3';

    $request = ServerRequestFactory::fromGlobals($server);
    $this->assertEquals(2, $request->getVersion());
  }


  /**
   * Ensures that the default version is 1, if no suitable header is found.
   *
   */
  public function testGetVersionSetsDefaultTo1() {

    $server['HTTP_ACCEPT'] = 'text/html';

    $request = ServerRequestFactory::fromGlobals($server);
    $this->assertEquals(1, $request->getVersion());
  }


  /**
   * Ensures that a version not associated with an application/json accept
   * header is not used.
   *
   */
  public function testGetVersionReturns1IfAcceptHeaderIsNotJson() {

    $server['HTTP_ACCEPT'] = 'text/html; version=2';

    $request = ServerRequestFactory::fromGlobals($server);
    $this->assertEquals(1, $request->getVersion());
  }


  /**
   * Ensures that isJson() works as expected.
   *
   */
  public function testIsJson() {

    $server['HTTP_CONTENT_TYPE'] = 'application/json';

    $request = ServerRequestFactory::fromGlobals($server);
    $this->assertTrue($request->isJson());

    // Test case insensitivity.
    $server['HTTP_CONTENT_TYPE'] = 'APPLICATION/JSON';

    $request = ServerRequestFactory::fromGlobals($server);
    $this->assertTrue($request->isJson());
  }


  /**
   * Ensures that getRequestId() works as expected.
   *
   */
  public function testRequestId() {

    $request = ServerRequestFactory::fromGlobals();
    $this->assertInternalType('string', $request->getRequestId());
  }


  /**
   * Ensures that getRequestId() returns the value of X-REQUEST-ID.
   *
   */
  public function testRequestIdReturnsHeaderValue() {

    $server['HTTP_X_REQUEST_ID'] = 'request-id';

    $request = ServerRequestFactory::fromGlobals($server);
    $this->assertEquals('request-id', $request->getRequestId());
  }


  /**
   * Ensures that getRequestId() returns the same value when called twice.
   *
   */
  public function testRequestIdReturnsSameValueWhenCalledMoreThanOnce() {

    $request = ServerRequestFactory::fromGlobals();
    $id = $request->getRequestId();

    $this->assertEquals($id, $request->getRequestId());
  }
}