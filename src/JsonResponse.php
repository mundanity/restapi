<?php

namespace Drupal\restapi;

use Symfony\Component\HttpFoundation\JsonResponse as SymfonyJsonResponse;


/**
 * Provides additional functionality to the Symfony JsonResponse object. Namely,
 * allows for the retrieval of the data after it has been originally set.
 *
 */
class JsonResponse extends SymfonyJsonResponse {

  /**
   * Returns the data contained in the response object as an array.
   *
   * @return array
   *
   */
  public function getData() {
    return json_decode($this->data, TRUE);
  }

}