<?php

namespace Drupal\restapi;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Zend\Diactoros\Response\EmptyResponse;


/**
 * Provides a base class Resource for modules to extend.
 *
 */
abstract class AbstractResource implements ResourceInterface {

  /**
   * A HTTP response factory.
   *
   * @var HttpResponseFactory
   *
   */
  protected $http = NULL;


  /**
   * A HTTP Request object.
   *
   * @var ServerRequestInterface
   */
  protected $request = NULL;

  /**
   * A Drupal user object.
   *
   * @var \StdClass
   */
  protected $user = NULL;


  /**
   * An array of required parameters.
   *
   * @var array
   *
   */
  protected $required_params = [];


  /**
   * {@inheritdoc}
   *
   */
  public function __construct(\StdClass $user, ServerRequestInterface $request, HttpResponseFactory $http = NULL) {
    $this->user    = $user;
    $this->request = $request;
    $this->http    = $http ?: new HttpResponseFactory();
  }


  /**
   * {@inheritdoc}
   *
   */
  public function requireParameter($name, callable $validator = NULL) {
    $this->required_params[$name] = $validator ?: TRUE;
  }


  /**
   * {@inheritdoc}
   *
   */
  public function getRequiredParameters() {
    return $this->required_params;
  }


  /**
   * {@inheritdoc}
   *
   */
  public function before() {}


  /**
   * {@inheritdoc}
   *
   */
  public function after(ResponseInterface $response) {}


  /**
   * Default OPTIONS handler.
   *
   */
  public function options() {

    $possible_methods = ['DELETE', 'GET', 'POST', 'PUT', 'OPTIONS'];
    $request          = $this->getRequest();
    $path             = $request->getUri()->getPath();
    $resource         = restapi_get_resource($path);

    // Check to see if there's a versioned class method for each possible HTTP verb
    $allowed_methods = array_filter($possible_methods, function ($method) use ($request, $resource) {
      return _restapi_get_versioned_method($resource, $request->withMethod($method));
    });

    $headers['Allow'] = implode(', ', $allowed_methods);

    // @todo add support for CORS preflight headers

    return new EmptyResponse(200, $headers);
  }


  /**
   * Returns the current request.
   *
   * @return ServerRequestInterface
   *
   */
  public function getRequest() {
    return $this->request;
  }


  /**
   * Returns the current user.
   *
   * @return \StdClass
   *
   */
  public function getUser() {
    return $this->user;
  }


  /**
   * {@inheritdoc}
   *
   */
  public function toJson($data, $status = 200) {
    return $this->http->toJson($data, $status);
  }


  /**
   * {@inheritdoc}
   *
   */
  public function to403($message = NULL) {
    return $this->http->to403($message);
  }


  /**
   * {@inheritdoc}
   *
   */
  public function to404($message = NULL) {
    return $this->http->to404($message);
  }


  /**
   * {@inheritdoc}
   *
   */
  public function toError($message, $code = 'system', $status = 500) {
    return $this->http->toError($message, $code, $status);
  }

}
