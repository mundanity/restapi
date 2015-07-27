<?php

namespace Drupal\restapi;

use Psr\Http\Message\StreamInterface;
use Zend\Diactoros\ServerRequestFactory as AbstractServerRequestFactory;


/**
 * Overrides the ServerRequestFactory to return our custom Request object.
 *
 */
class ServerRequestFactory extends AbstractServerRequestFactory {

  /**
   * {@inheritdoc}
   *
   * @param StreamInterface $content
   *   (Optional) The content to override the input stream with.
   *
   */
  public static function fromGlobals(
    array $server = null,
    array $query = null,
    array $body = null,
    array $cookies = null,
    array $files = null,
    StreamInterface $content = null
  ) {
    $server  = static::normalizeServer($server ?: $_SERVER);
    $files   = static::normalizeFiles($files ?: $_FILES);
    $headers = static::marshalHeaders($server);

    // static::get() has a default parameter, however, if the header is set but
    // the value is NULL, e.g. during a drush operation, the NULL result is
    // returned, instead of the default.
    $method  = strtoupper(static::get('REQUEST_METHOD', $server) ?: 'GET');

    $request = new JsonRequest(
      $server,
      $files,
      static::marshalUriFromServer($server, $headers),
      $method,
      $content ?: 'php://input',
      $headers
    );

    $is_json = strpos(static::get('CONTENT_TYPE', $server), 'application/json') !== FALSE;
    $vars_in_body = in_array($method, ['PUT', 'POST', 'PATCH', 'DELETE']);

    if ($vars_in_body) {

      $data = $content ? $content->getContents() : file_get_contents('php://input');

      if ($is_json) {
        $body = json_decode($data, TRUE);
      } else {
        parse_str($data, $body);
      }
    }

    return $request
      ->withCookieParams($cookies ?: $_COOKIE)
      ->withQueryParams($query ?: $_GET)
      ->withParsedBody($body ?: $_POST);
  }
}