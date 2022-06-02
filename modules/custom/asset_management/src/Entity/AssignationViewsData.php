<?php

namespace Drupal\asset_management\Entity;

use Drupal\views\EntityViewsData;

/**
 * Provides Views data for Assignation entities.
 */
class AssignationViewsData extends EntityViewsData {

  /**
   * {@inheritdoc}
   */
  public function getViewsData() {
    $data = parent::getViewsData();

    // Additional information for Views integration, such as table joins, can be
    // put here.
    return $data;
  }

}
