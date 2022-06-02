<?php

/**
 * @file
 * Contains maintenance_record.page.inc.
 *
 * Page callback for Maintenance record entities.
 */

use Drupal\Core\Render\Element;

/**
 * Prepares variables for Maintenance record templates.
 *
 * Default template: maintenance_record.html.twig.
 *
 * @param array $variables
 *   An associative array containing:
 *   - elements: An associative array containing the user information and any
 *   - attributes: HTML attributes for the containing element.
 */
function template_preprocess_maintenance_record(array &$variables) {
  // Fetch MaintenanceRecord Entity Object.
  $maintenance_record = $variables['elements']['#maintenance_record'];

  // Helpful $content variable for templates.
  foreach (Element::children($variables['elements']) as $key) {
    $variables['content'][$key] = $variables['elements'][$key];
  }
}
