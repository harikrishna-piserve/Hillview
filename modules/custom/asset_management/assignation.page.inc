<?php

/**
 * @file
 * Contains assignation.page.inc.
 *
 * Page callback for Assignation entities.
 */

use Drupal\Core\Render\Element;

/**
 * Prepares variables for Assignation templates.
 *
 * Default template: assignation.html.twig.
 *
 * @param array $variables
 *   An associative array containing:
 *   - elements: An associative array containing the user information and any
 *   - attributes: HTML attributes for the containing element.
 */
function template_preprocess_assignation(array &$variables) {
  // Fetch Assignation Entity Object.
  $assignation = $variables['elements']['#assignation'];

  // Helpful $content variable for templates.
  foreach (Element::children($variables['elements']) as $key) {
    $variables['content'][$key] = $variables['elements'][$key];
  }
}
