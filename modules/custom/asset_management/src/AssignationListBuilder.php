<?php

namespace Drupal\asset_management;

use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Entity\EntityListBuilder;
use Drupal\Core\Link;

/**
 * Defines a class to build a listing of Assignation entities.
 *
 * @ingroup asset_management
 */
class AssignationListBuilder extends EntityListBuilder {

  /**
   * {@inheritdoc}
   */
  public function buildHeader() {
    $header['id'] = $this->t('Assignation ID');
    // $header['name'] = $this->t('Name');
    return $header + parent::buildHeader();
  }

  /**
   * {@inheritdoc}
   */
  public function buildRow(EntityInterface $entity) {
    /* @var \Drupal\asset_management\Entity\Assignation $entity */
    $row['id'] = $entity->id();
    // $row['name'] = Link::createFromRoute(
    //   $entity->label(),
    //   'entity.assignation.edit_form',
    //   ['assignation' => $entity->id()]
    // );
    return $row + parent::buildRow($entity);
  }

}
