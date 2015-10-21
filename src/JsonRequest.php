<?php

namespace Drupal\restapi;

use Zend\Diactoros\ServerRequest;


/**
 * Represents a HTTP request. For PUT / PATCH / POST / DELETE requests,
 * translates a json body into request variables.
 *
 */
class JsonRequest extends ServerRequest {

  /**
   * Holds the request ID.
   *
   * @var string
   */
  protected $request_id = NULL;


  /**
   * Retrieves a parameter from the request, depending on the HTTP method.
   *
   * @param string $param
   *   The parameter to return.
   * @param mixed $default
   *   The default to return, if the parameter does not exist.
   *
   * @return mixed
   *
   */
  public function get($param, $default = NULL) {
    $method = strtolower($this->getMethod());
    $items  = ($method == 'get' || $method == 'delete') ? $this->getQueryParams() : $this->getParsedBody();
    return isset($items[$param]) ? $items[$param] : $default;
  }


  /**
   * Sets data on the request object. This will set the appropriate data
   * depending on the request method.
   *
   * @param array $data
   *
   * @return self
   */
  public function withData(array $data = []) {

    $func = strtolower($this->getMethod()) == 'get' ? 'QueryParams' : 'ParsedBody';
    $get_func  = 'get' . $func;
    $with_func = 'with' . $func;
    $data = array_merge($this->$get_func(), $data);

    return $this->$with_func($data);
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

    // We'll assume the first accept header to have a version is accurate.
    preg_match('/application\/json;\s+version=(\d+)/i', $this->getHeaderLine('accept'), $matches);

    if (isset($matches[1])) {
      $version = $matches[1];
    }

    return $version;

  }


  /**
   * Determines if this request is actually a Json request or not.
   *
   * A Json request will have a Content-type header of "application/json".
   * Although the HTTP spec does suggest that header values are case-sensitive
   * (or rather, does not specify that they are case-insensitive), we'll accept
   * differently cased strings here.
   *
   * @return boolean
   *
   */
  public function isJson() {
    return (stripos($this->getHeaderLine('content-type'), 'application/json') === 0);
  }


  /**
   * Returns the ID of this request.
   *
   * @param callable $id_func
   *   The callable to use to generate the request ID if the header X-REQUEST-ID
   *   is not set. Defaults to PHP's inbuilt "uniqid".
   *
   * @return string
   *
   */
  public function getRequestId(callable $id_func = null) {

    if ($this->request_id !== NULL) {
      return $this->request_id;
    }

    $id_func = $id_func ?: 'uniqid';

    if (!($this->request_id = $this->getHeaderLine('x-request-id'))) {
      $this->request_id = call_user_func($id_func);
    }

    // Note that the type casting is explicit here, in case the callable returns
    // an object that implements __toString().
    return (string) $this->request_id;

  }

}