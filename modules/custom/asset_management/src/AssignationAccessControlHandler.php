<?php

namespace Drupal\asset_management;

use Drupal\Core\Entity\EntityAccessControlHandler;
use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Session\AccountInterface;
use Drupal\Core\Access\AccessResult;

/**
 * Access controller for the Assignation entity.
 *
 * @see \Drupal\asset_management\Entity\Assignation.
 */
class AssignationAccessControlHandler extends EntityAccessControlHandler {

  /**
   * {@inheritdoc}
   */
  protected function checkAccess(EntityInterface $entity, $operation, AccountInterface $account) {
    /** @var \Drupal\asset_management\Entity\AssignationInterface $entity */

    switch ($operation) {

      case 'view':

        if (!$entity->isPublished()) {
          return AccessResult::allowedIfHasPermission($account, 'view unpublished assignation entities');
        }


        return AccessResult::allowedIfHasPermission($account, 'view published assignation entities');

      case 'update':

        return AccessResult::allowedIfHasPermission($account, 'edit assignation entities');

      case 'delete':

        return AccessResult::allowedIfHasPermission($account, 'delete assignation entities');
    }

    // Unknown operation, no opinion.
    return AccessResult::neutral();
  }

  /**
   * {@inheritdoc}
   */
  protected function checkCreateAccess(AccountInterface $account, array $context, $entity_bundle = NULL) {
    return AccessResult::allowedIfHasPermission($account, 'add assignation entities');
  }


}
