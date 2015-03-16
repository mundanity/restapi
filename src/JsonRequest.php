<?php

namespace Drupal\restapi;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\AcceptHeader;


/**
 * Represents a HTTP request. For PUT / PATCH / POST / DELETE requests,
 * translates a json body into request variables.
 *
 */
class JsonRequest extends Request {

  /**
   * Holds the request ID.
   *
   * @var string
   */
  protected $request_id = NULL;


  /**
   * {@inheritdoc}
   *
   */
  public function initialize(array $query = [], array $request = [], array $attributes = [], array $cookies = [], array $files = [], array $server = [], $content = null) {
    parent::initialize($query, $request, $attributes, $cookies, $files, $server, $content);

    $vars_in_body = in_array($this->getMethod(), ['PUT', 'POST', 'PATCH', 'DELETE']);

    if (!$this->isJson() || !$vars_in_body) {
      return;
    }

    if (!($data = json_decode($this->getContent(), TRUE))) {
      return;
    }

    foreach($data as $key => $value) {
      $this->request->set($key, $value);
    }
  }


  /**
   * Returns the API version.
   *
   * @return int $version
   *   The version number of the API (default: 1).
   *
   */
  public function getVersion() {

    $version = 1;
    $accept  = AcceptHeader::fromString($this->headers->get('accept'));

    // We'll assume the first accept header to have a version is accurate.
    foreach($accept->all() as $item) {
      if ($item->getAttribute('version')) {
        $version = $item->getAttribute('version');
        break;
      }
    }

    return $version;

  }


  /**
   * Determines if this request is actually a Json request or not.
   *
   * A Json request will have an accept header of "application/json".
   *
   * @return boolean
   *
   */
  public function isJson() {
    return (strpos($this->headers->get('content-type'), 'application/json') === 0);
  }


  /**
   * Returns the ID of this request.
   *
   * @return string
   *
   */
  public function getRequestId() {

    if ($this->request_id !== NULL) {
      return $this->request_id;
    }

    if (!($this->request_id = $this->headers->get('x-request-id'))) {
      $this->request_id = uniqid('', TRUE);
    }

    return $this->request_id;

  }

}