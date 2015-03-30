<?php

namespace Drupal\restapi\Exception;

use Exception;


/**
 * Ensures that the provided authentication class exists and is an
 * implementation of AuthenticationServiceInterface
 *
 */
class AuthClassNotValidException extends Exception {}