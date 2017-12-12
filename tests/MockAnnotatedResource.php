<?php

namespace Drupal\restapi\Test;

use Drupal\restapi\AbstractResource;


/**
 * Resource used to test annotation parsing.
 *
 */
class MockAnnotatedResource extends AbstractResource {

  /**
   *
   */
  public function notDeprecated() {}


  /**
   * @deprecated
   *
   */
  public function deprecatedNoVersionNoReason() {}


  /**
   * @deprecated 1
   *
   */
  public function deprecatedVersionNoReason() {}


  /**
   * @deprecated v2
   *
   */
  public function deprecatedPrefixedVersionNoReason() {}


  /**
   * @deprecated 3 Example reason
   *
   */
  public function deprecatedVersionReason() {}


  /**
   * @deprecated v4 Example reason
   *
   */
  public function deprecatedPrefixedVersionReason() {}


  /**
   * @deprecated Example reason
   *
   */
  public function deprecatedNoVersionReason() {}


  /**
   *
   */
  public function stabilityNotSpecified() {}


  /**
   * @stability prototype
   *
   */
  public function stabilitySpecified() {}

}
