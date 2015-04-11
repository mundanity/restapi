<?php

use Drupal\restapi\JsonRequest;


class JsonRequestTest extends PHPUnit_Framework_TestCase {

  public function setUp() {
    $this->server = [
      'HTTP_CONTENT_TYPE' => 'application/json',
      'HTTP_ACCEPT'       => 'application/json; version=3',
      'HTTP_X_REQUEST_ID' => 'client-set-id',
    ];
    $this->content = json_encode(['var' => 'set']);
  }


  public function testInitializeIgnoresNonJsonContentType() {

    $server = [
      'HTTP_CONTENT_TYPE' => 'text/html',
    ];

    $request = new JsonRequest();
    $request->initialize(
      [], // Query
      [], // Request
      [], // Attributes
      [], // Cookies
      [], // Files
      $server, // Server
      $this->content
    );

    $this->assertNull($request->get('var'));

  }


  public function testInitializeSetsVariableForPut() {

    $this->server['REQUEST_METHOD'] = 'PUT';

    $request = new JsonRequest();
    $request->initialize(
      [], // Query
      [], // Request
      [], // Attributes
      [], // Cookies
      [], // Files
      $this->server, // Server
      $this->content
    );

    $this->assertEquals('set', $request->get('var'));

  }


  public function testInitializeSetsVariableForPost() {

    $this->server['REQUEST_METHOD'] = 'POST';

    $request = new JsonRequest();
    $request->initialize(
      [], // Query
      [], // Request
      [], // Attributes
      [], // Cookies
      [], // Files
      $this->server, // Server
      $this->content
    );

    $this->assertEquals('set', $request->get('var'));

  }


  public function testInitializeSetsVariableForPatch() {

    $this->server['REQUEST_METHOD'] = 'PATCH';

    $request = new JsonRequest();
    $request->initialize(
      [], // Query
      [], // Request
      [], // Attributes
      [], // Cookies
      [], // Files
      $this->server, // Server
      $this->content
    );

    $this->assertEquals('set', $request->get('var'));

  }


  public function testInitializeSetsVariableForDelete() {

    $this->server['REQUEST_METHOD'] = 'DELETE';

    $request = new JsonRequest();
    $request->initialize(
      [], // Query
      [], // Request
      [], // Attributes
      [], // Cookies
      [], // Files
      $this->server, // Server
      $this->content
    );

    $this->assertEquals('set', $request->get('var'));

  }


  public function testInitializeOverridesRequestVariable() {

    $this->server['REQUEST_METHOD'] = 'POST';

    $request = new JsonRequest();
    $request->initialize(
      [], // Query
      ['var' => 'unexpected'], // Request
      [], // Attributes
      [], // Cookies
      [], // Files
      $this->server, // Server
      $this->content
    );

    $this->assertEquals('set', $request->get('var'));

  }


  public function testGetVersion() {

    $request = new JsonRequest();
    $request->initialize(
      [], // Query
      [], // Request
      [], // Attributes
      [], // Cookies
      [], // Files
      $this->server, // Server
      $this->content
    );

    $this->assertEquals(3, $request->getVersion());

  }


  public function testFirstVersionHeaderIsUsed() {

    $this->server['HTTP_ACCEPT'] = 'application/json; version=2, text/html; version=3';

    $request = new JsonRequest();
    $request->initialize(
      [], // Query
      [], // Request
      [], // Attributes
      [], // Cookies
      [], // Files
      $this->server, // Server
      $this->content
    );

    $this->assertEquals(2, $request->getVersion());

  }


  public function testGetVersionSetsDefaultTo1() {

    $this->server['HTTP_ACCEPT'] = 'text/html';

    $request = new JsonRequest();
    $request->initialize(
      [], // Query
      [], // Request
      [], // Attributes
      [], // Cookies
      [], // Files
      $this->server, // Server
      $this->content
    );

    $this->assertEquals(1, $request->getVersion());

  }


  public function testClientSetRequestId() {

    $request = new JsonRequest();
    $request->initialize(
      [], // Query
      [], // Request
      [], // Attributes
      [], // Cookies
      [], // Files
      $this->server, // Server
      $this->content
    );

    $this->assertEquals('client-set-id', $request->getRequestId());

  }


  public function testRequestIdIsSameWhenCalledTwice() {

    unset($this->server['HTTP_X_REQUEST_ID']);

    $request = new JsonRequest();
    $request->initialize(
      [], // Query
      [], // Request
      [], // Attributes
      [], // Cookies
      [], // Files
      $this->server, // Server
      $this->content
    );
    $request_id = $request->getRequestId();

    $this->assertEquals($request_id, $request->getRequestId());

  }


  public function testSetDataWorksAsAdvertised() {

    $request = new JsonRequest();
    $request->initialize(
      ['foo' => 'fooget', 'bar' => 'barget'], // Query
      ['foo' => 'foopost', 'bar' => 'barpost'], // Request
      [], // Attributes
      [], // Cookies
      [], // Files
      $this->server, // Server
      $this->content
    );

    $data = [
      'new' => 'value',
      'foo' => 'newfoo',
    ];

    $request->setData($data);

    $this->assertEquals($request->query->get('new'), 'value');
    $this->assertEquals($request->query->get('foo'), 'newfoo');

    $request->setMethod('PATCH');

    $request->setData($data);

    $this->assertEquals($request->request->get('new'), 'value');
    $this->assertEquals($request->request->get('foo'), 'newfoo');

  }

}