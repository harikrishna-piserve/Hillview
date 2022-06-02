<?php

/**
 * @file
 * Contains asset.page.inc.
 *
 * Page callback for Asset entities.
 */

use Drupal\Core\Render\Element;

/**
 * Prepares variables for Asset templates.
 *
 * Default template: asset.html.twig.
 *
 * @param array $variables
 *   An associative array containing:
 *   - elements: An associative array containing the user information and any
 *   - attributes: HTML attributes for the containing element.
 */
function template_preprocess_asset(array &$variables) {
  // Fetch Asset Entity Object.
  $asset = $variables['elements']['#asset'];

  // Helpful $content variable for templates.
  foreach (Element::children($variables['elements']) as $key) {
    $variables['content'][$key] = $variables['elements'][$key];
  }
}
