<?php

namespace Drupal\restapi;

use Symfony\Component\HttpFoundation\Response;


/**
 * Provides a paginated response, inline with Heroku API guidlines on range
 * responses.
 *
 * @see https://devcenter.heroku.com/articles/platform-api-reference#ranges
 * @see https://github.com/interagent/http-api-design/issues/36
 *
 */
class RangeResponse extends Response {


  public function __construct($content = '', $status = 206, $headers = []) {

    parent::__construct($content, $status, $headers);
    $this->headers->set('Content-Range', "hello");
  }

}
