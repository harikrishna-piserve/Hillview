<?php

/**
 * @file
 * Contains vendor_record.page.inc.
 *
 * Page callback for Vendor record entities.
 */

use Drupal\Core\Render\Element;

/**
 * Prepares variables for Vendor record templates.
 *
 * Default template: vendor_record.html.twig.
 *
 * @param array $variables
 *   An associative array containing:
 *   - elements: An associative array containing the user information and any
 *   - attributes: HTML attributes for the containing element.
 */
function template_preprocess_vendor_record(array &$variables) {
  // Fetch VendorRecord Entity Object.
  $vendor_record = $variables['elements']['#vendor_record'];

  // Helpful $content variable for templates.
  foreach (Element::children($variables['elements']) as $key) {
    $variables['content'][$key] = $variables['elements'][$key];
  }
}
