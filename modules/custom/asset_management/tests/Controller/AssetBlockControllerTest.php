<?php

namespace Drupal\asset_management\Tests;

use Drupal\simpletest\WebTestBase;

/**
 * Provides automated tests for the asset_management module.
 */
class AssetBlockControllerTest extends WebTestBase {


  /**
   * {@inheritdoc}
   */
  public static function getInfo() {
    return [
      'name' => "asset_management AssetBlockController's controller functionality",
      'description' => 'Test Unit for module asset_management and controller AssetBlockController.',
      'group' => 'Other',
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function setUp() {
    parent::setUp();
  }

  /**
   * Tests asset_management functionality.
   */
  public function testAssetBlockController() {
    // Check that the basic functions of module asset_management.
    $this->assertEquals(TRUE, TRUE, 'Test Unit Generated via Drupal Console.');
  }

}
