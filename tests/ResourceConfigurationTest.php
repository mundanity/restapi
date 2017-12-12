<?php

use Drupal\restapi\ResourceConfiguration;
use Drupal\restapi\Test\MockAnnotatedResource;


/**
 * Tests for our ResourceConfiguration class.
 *
 */
class ResourceConfigurationTest extends PHPUnit_Framework_TestCase {

  public function setUp() {
    $this->class = 'Drupal\restapi\AbstractResource';
    $this->auth  = 'Drupal\restapi\Auth\DrupalAuthenticationService';
  }


  public function testConstructorThrowsExceptionWhenClassDoesNotExist() {
    $this->setExpectedException('Drupal\restapi\Exception\ClassNotValidException');
    $config = new ResourceConfiguration('path/to/resource', 'my_module', 'ClassDoesNotExist', $this->auth);
  }


  public function testConstructorThrowsExceptionWhenInterfaceMismatches() {
    $this->setExpectedException('Drupal\restapi\Exception\ClassNotValidException');
    $config = new ResourceConfiguration('path/to/resource', 'my_module', 'ClassExistsButDoesNotSatisfyInterface', $this->auth);
  }


  public function testConstructorThrowsExceptionWhenAuthClassDoesNotExist() {
    $this->setExpectedException('Drupal\restapi\Exception\AuthClassNotValidException');
    $config = new ResourceConfiguration('path/to/resource', 'my_module', $this->class, 'ClassDoesNotExist');
  }


  public function testConstructorThrowsExceptionWhenAuthInterfaceMismatches() {
    $this->setExpectedException('Drupal\restapi\Exception\AuthClassNotValidException');
    $config = new ResourceConfiguration('path/to/resource', 'my_module', $this->class, 'ClassExistsButDoesNotSatisfyInterface');
  }


  public function testGettersWork() {
    $config = new ResourceConfiguration('path/%/resource', 'my_module', $this->class, $this->auth);

    $this->assertEquals('path/%/resource', $config->getPath());
    $this->assertEquals('my_module', $config->getModule());
    $this->assertEquals($this->class, $config->getClass());
    $this->assertEquals($this->auth, $config->getAuthenticationClass());
  }


  public function testGetArguments() {

    $config = new ResourceConfiguration('path/%/resource', 'my_module', $this->class, $this->auth);
    $args   = $config->getArgumentsForPath('path/to/resource');

    $this->assertEquals($args, ['to']);

    $config = new ResourceConfiguration('path/%/%', 'my_module', $this->class, $this->auth);
    $args   = $config->getArgumentsForPath('path/to/resource');

    $this->assertEquals($args, ['to', 'resource']);

    $config = new ResourceConfiguration('path/%/resource', 'my_module', $this->class, $this->auth);
    $args   = $config->getArgumentsForPath('this/should/be/empty/array');

    $this->assertEquals($args, []);

  }


  public function testMatchesPath() {
    $config = new ResourceConfiguration('path/%/resource', 'my_module', $this->class, $this->auth);

    $this->assertTrue($config->matchesPath('path/valid/resource'));
    $this->assertFalse($config->matchesPath('path/not/valid/resource'));

    $config = new ResourceConfiguration('path/matches/%', 'my_module', $this->class, $this->auth);

    $this->assertTrue($config->matchesPath('path/matches/resource'));
    $this->assertFalse($config->matchesPath('path/not/valid/resource'));

    $config = new ResourceConfiguration('path/%/%', 'my_module', $this->class, $this->auth);

    $this->assertTrue($config->matchesPath('path/matches/resource'));
    $this->assertFalse($config->matchesPath('path/not/valid/resource'));

  }


  public function testUrlPrefix() {

    $config = new ResourceConfiguration('path/to/resource', 'my_module', $this->class, $this->auth, 'myprefix');
    $this->assertEquals('myprefix/path/to/resource', $config->getPath());

    $config = new ResourceConfiguration('/path/to/resource', 'my_module', $this->class, $this->auth, '/myprefix/');
    $this->assertEquals('myprefix/path/to/resource', $config->getPath());

  }


  /**
   * Ensures deprecation parsing works as expected.
   *
   * @dataProvider deprecatedAnnotationProvider
   *
   */
  public function testGetDeprecationForMethod($method, $version, $reason) {

    $config = new ResourceConfiguration('path/to/resource', 'my_module', MockAnnotatedResource::class, $this->auth);

    $deprecation = $config->getDeprecationForMethod($method);

    $this->assertInternalType('array', $deprecation);
    $this->assertArrayHasKey('version', $deprecation);
    $this->assertArrayHasKey('reason', $deprecation);
    $this->assertEquals($version, $deprecation['version']);
    $this->assertEquals($reason, $deprecation['reason']);
  }


  /**
   * Ensures getDeprecationForMethod() returns NULL if the endpoint is not deprecated.
   *
   */
  public function testGetDeprecationForMethodForUndeprecatedMethod() {

    $config = new ResourceConfiguration('path/to/resource', 'my_module', MockAnnotatedResource::class, $this->auth);

    $this->assertNull($config->getDeprecationForMethod('notDeprecated'));
  }


  /**
   * Ensures stability parsing works as expected.
   *
   * @dataProvider stabilityAnnotationProvider
   *
   */
  public function testGetStabilityForMethod($method, $stability) {

    $config = new ResourceConfiguration('path/to/resource', 'my_module', MockAnnotatedResource::class, $this->auth);

    $this->assertEquals($stability, $config->getStabilityForMethod($method));
  }


  /**
   * Provides sample data for testing deprecation parsing.
   *
   */
  public function deprecatedAnnotationProvider() {

    //              method                               version  reason
    $scenarios[] = ['deprecatedNoVersionNoReason',       NULL,    NULL];
    $scenarios[] = ['deprecatedVersionNoReason',         1,       NULL];
    $scenarios[] = ['deprecatedPrefixedVersionNoReason', 2,       NULL];
    $scenarios[] = ['deprecatedVersionReason',           3,       'Example reason'];
    $scenarios[] = ['deprecatedPrefixedVersionReason',   4,       'Example reason'];
    $scenarios[] = ['deprecatedNoVersionReason',         NULL,    'Example reason'];

    return $scenarios;
  }


  /**
   * Provides sample data for testing stability parsing.
   *
   */
  public function stabilityAnnotationProvider() {

    //              method                   stability
    $scenarios[] = ['stabilityNotSpecified', 'production'];
    $scenarios[] = ['stabilitySpecified',    'prototype'];

    return $scenarios;
  }

}




/**
 * Fake class to satisfy test cases.
 *
 */
class ClassExistsButDoesNotSatisfyInterface {}