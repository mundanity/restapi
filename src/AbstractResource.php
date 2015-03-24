<?php

namespace Drupal\restapi;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;


/**
 * Provides a base class Resource for modules to extend.
 *
 */
abstract class AbstractResource implements ResourceInterface {

  /**
   * A HTTP Request object.
   *
   * @var Request
   */
  protected $request = NULL;

  /**
   * A Drupal user object.
   *
   * @var \StdClass
   */
  protected $user = NULL;


  /**
   * {@inheritdoc}
   *
   */
  public function __construct(\StdClass $user, Request $request) {
    $this->user = $user;
    $this->request = $request;
  }


  /**
   * {@inheritdoc}
   *
   */
  public function access($method = 'get') {
    return TRUE;
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
  public function after(Response $response) {}


  /**
   * Returns the current request.
   *
   * @return Request
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

}
