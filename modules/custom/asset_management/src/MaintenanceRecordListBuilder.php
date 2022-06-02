<?php

namespace Drupal\asset_management;

use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Entity\EntityListBuilder;
use Drupal\Core\Link;

/**
 * Defines a class to build a listing of Maintenance record entities.
 *
 * @ingroup asset_management
 */
class MaintenanceRecordListBuilder extends EntityListBuilder {

  /**
   * {@inheritdoc}
   */
  public function buildHeader() {
    $header['id'] = $this->t('Maintenance record ID');
    $header['name'] = $this->t('Name');
    return $header + parent::buildHeader();
  }

  /**
   * {@inheritdoc}
   */
  public function buildRow(EntityInterface $entity) {
    /* @var \Drupal\asset_management\Entity\MaintenanceRecord $entity */
    $row['id'] = $entity->id();
    $row['name'] = Link::createFromRoute(
      $entity->label(),
      'entity.maintenance_record.edit_form',
      ['maintenance_record' => $entity->id()]
    );
    return $row + parent::buildRow($entity);
  }

}
