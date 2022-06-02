<?php

namespace Drupal\asset_management;

use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Entity\EntityListBuilder;
use Drupal\Core\Link;

/**
 * Defines a class to build a listing of Vendor record entities.
 *
 * @ingroup asset_management
 */
class VendorRecordListBuilder extends EntityListBuilder {

  /**
   * {@inheritdoc}
   */
  public function buildHeader() {
    $header['id'] = $this->t('Vendor record ID');
    $header['name'] = $this->t('Name');
    return $header + parent::buildHeader();
  }

  /**
   * {@inheritdoc}
   */
  public function buildRow(EntityInterface $entity) {
    /* @var \Drupal\asset_management\Entity\VendorRecord $entity */
    $row['id'] = $entity->id();
    $row['name'] = Link::createFromRoute(
      $entity->label(),
      'entity.vendor_record.edit_form',
      ['vendor_record' => $entity->id()]
    );
    return $row + parent::buildRow($entity);
  }

}
