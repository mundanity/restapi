<?php

namespace Drupal\restapi;

use Psr\Http\Message\ServerRequestInterface;


/**
 * Provides a base class Resource for modules to extend.
 *
 */
abstract class AbstractResource implements ResourceInterface {

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
  public function __construct(\StdClass $user, ServerRequestInterface $request) {
    $this->user = $user;
    $this->request = $request;
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
  public function after(JsonResponse $response) {}


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
    return JsonResponse::create($data, $status);
  }


  /**
   * {@inheritdoc}
   *
   */
  public function to403($message = NULL) {
    $message = $message ?: 'Permission denied';
    return $this->toError($message, 'unauthenticated', 403);
  }


  /**
   * {@inheritdoc}
   *
   */
  public function to404($message = NULL) {
    $message = $message ?: 'Resource not found';
    return $this->toError($message, 'not_found', 404);
  }


  /**
   * {@inheritdoc}
   *
   */
  public function toError($message, $code = 'system', $status = 500) {
    $data = [
      'error'   => $code,
      'message' => $message,
    ];
    return $this->toJson($data, $status);
  }

}
