<?php

namespace Drupal\asset_management;

use Drupal\Core\Entity\EntityAccessControlHandler;
use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Session\AccountInterface;
use Drupal\Core\Access\AccessResult;

/**
 * Access controller for the Vendor record entity.
 *
 * @see \Drupal\asset_management\Entity\VendorRecord.
 */
class VendorRecordAccessControlHandler extends EntityAccessControlHandler {

  /**
   * {@inheritdoc}
   */
  protected function checkAccess(EntityInterface $entity, $operation, AccountInterface $account) {
    /** @var \Drupal\asset_management\Entity\VendorRecordInterface $entity */

    switch ($operation) {

      case 'view':

        if (!$entity->isPublished()) {
          return AccessResult::allowedIfHasPermission($account, 'view unpublished vendor record entities');
        }


        return AccessResult::allowedIfHasPermission($account, 'view published vendor record entities');

      case 'update':

        return AccessResult::allowedIfHasPermission($account, 'edit vendor record entities');

      case 'delete':

        return AccessResult::allowedIfHasPermission($account, 'delete vendor record entities');
    }

    // Unknown operation, no opinion.
    return AccessResult::neutral();
  }

  /**
   * {@inheritdoc}
   */
  protected function checkCreateAccess(AccountInterface $account, array $context, $entity_bundle = NULL) {
    return AccessResult::allowedIfHasPermission($account, 'add vendor record entities');
  }


}
