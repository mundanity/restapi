<?php

namespace Drupal\restapi;

use Zend\Diactoros\Response\JsonResponse as ZendJsonResponse;


/**
 * Provides additional functionality to the Guzzle PSR-7 Response object.
 *
 */
class JsonResponse extends ZendJsonResponse {

  /**
   * Factory method to create a response.
   *
   * @param mixed $data
   *   A string or StreamInterface implementation.
   * @param int $status
   *   The HTTP status to use.
   * @param array $headers
   *   An array of headers.
   *
   * @return self
   *
   */
  public static function create($data, $status = 200, array $headers = []) {
    return new static($data, $status, $headers, JSON_PRETTY_PRINT);
  }


  /**
   * Returns the data contained in the response object as an array.
   *
   * @return array
   *
   */
  public function getData() {
    return json_decode((string) $this->getBody(), TRUE);
  }

}