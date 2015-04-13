<?php

use Drupal\restapi\JsonResponse;


/**
 * The error handler for when Drupal encounters an error not typically handled
 * by the Drupal\restapi\Api class.
 *
 * @param array $vars
 *   An array of processed theme variables.
 *
 * @see restapi_theme_registry_alter()
 *
 */
function theme_restapi_error($vars) {

  $data = [
    'error'   => 'system',
    'message' => strip_tags($vars['content']),
  ];

  $response = JsonResponse::create($data, 500);
  restapi_delivery_callback($response);

}