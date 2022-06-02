<?php

namespace Drupal\Tests\form_mode_manager\Kernel;

use Drupal\KernelTests\KernelTestBase;

/**
 * Tests the routes generated by form_mode_manager.
 *
 * @group form_mode_manager
 */
class FormModeManagerRouteTest extends KernelTestBase {

  /**
   * Modules to enable.
   *
   * @var array
   */
  protected static $modules = [
    'system',
    'form_mode_manager',
  ];

  /**
   * Tests the Plugin to retrieve dynamically the entity route name.
   *
   * @param string $entity_type_id
   *   The name of entity type id to test.
   * @param string $operation
   *   The entity form operation name to test.
   * @param string $route_name_expected
   *   The route name expected.
   *
   * @dataProvider providerEntityRouteInfos
   */
  public function testFormModeManagerPlugin($entity_type_id, $operation, $route_name_expected) {
    /** @var \Drupal\form_mode_manager\EntityRoutingMapBase $route_properties_plugin */
    $route_properties_plugin = \Drupal::service('plugin.manager.entity_routing_map')->createInstance($entity_type_id, ['entityTypeId' => $entity_type_id]);
    $this->assertEquals($route_name_expected, $route_properties_plugin->getOperation($operation), 'Operation ' . $operation . ' correctly retrieved for ' . $entity_type_id . ' entity.');
  }

  /**
   * Provides test data for testFormModeMangerPlugin().
   *
   * @see self::testFormModeManagerPlugin()
   */
  public function providerEntityRouteInfos() {
    $data = [];
    $data[] = ['node', 'add_form', 'node.add'];
    $data[] = ['node', 'edit_form', 'entity.node.edit_form'];
    $data[] = ['user', 'add_form', 'user.register'];
    $data[] = ['user', 'edit_form', 'entity.user.edit_form'];
    $data[] = ['user', 'admin_add', 'user.admin_create'];
    $data[] = ['block_content', 'add_form', 'block_content.add_form'];
    $data[] = ['block_content', 'edit_form', 'entity.block_content.edit_form'];
    $data[] = ['taxonomy_term', 'add_form', 'entity.taxonomy_term.add_form'];
    $data[] = ['taxonomy_term', 'edit_form', 'entity.taxonomy_term.edit_form'];
    $data[] = ['media', 'add_form', 'entity.media.add_form'];
    $data[] = ['media', 'edit_form', 'entity.media.edit_form'];
    return $data;
  }

  /**
   * Asserts that admin routes are correctly marked as such.
   *
   * @param string $route_name
   *   The route name expected.
   *
   * @dataProvider providerAdminRoutes
   */
  public function testAdminRoutes($route_name) {
    $route = \Drupal::service('router.route_provider')
      ->getRouteByName($route_name);
    $is_admin = \Drupal::service('router.admin_context')
      ->isAdminRoute($route);
    $this->assertTrue($is_admin, 'Admin route correctly marked for "Form Mode Manager settings" pages.');
  }

  /**
   * Provides test data for testAdminRoutes().
   *
   * @see \Drupal\Tests\form_mode_manager\Functional\FormModeManagerRouteTest::testAdminRoutes()
   */
  public function providerAdminRoutes() {
    $data = [];
    $data[] = ['form_mode_manager.admin_settings'];
    $data[] = ['form_mode_manager.admin_settings_links_task'];
    return $data;
  }

}