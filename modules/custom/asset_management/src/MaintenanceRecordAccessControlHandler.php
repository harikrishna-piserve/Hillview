<?php

namespace Drupal\asset_management;

use Drupal\Core\Entity\EntityAccessControlHandler;
use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Session\AccountInterface;
use Drupal\Core\Access\AccessResult;

/**
 * Access controller for the Maintenance record entity.
 *
 * @see \Drupal\asset_management\Entity\MaintenanceRecord.
 */
class MaintenanceRecordAccessControlHandler extends EntityAccessControlHandler {

  /**
   * {@inheritdoc}
   */
  protected function checkAccess(EntityInterface $entity, $operation, AccountInterface $account) {
    /** @var \Drupal\asset_management\Entity\MaintenanceRecordInterface $entity */

    switch ($operation) {

      case 'view':

        if (!$entity->isPublished()) {
          return AccessResult::allowedIfHasPermission($account, 'view unpublished maintenance record entities');
        }


        return AccessResult::allowedIfHasPermission($account, 'view published maintenance record entities');

      case 'update':

        return AccessResult::allowedIfHasPermission($account, 'edit maintenance record entities');

      case 'delete':

        return AccessResult::allowedIfHasPermission($account, 'delete maintenance record entities');
    }

    // Unknown operation, no opinion.
    return AccessResult::neutral();
  }

  /**
   * {@inheritdoc}
   */
  protected function checkCreateAccess(AccountInterface $account, array $context, $entity_bundle = NULL) {
    return AccessResult::allowedIfHasPermission($account, 'add maintenance record entities');
  }


}
