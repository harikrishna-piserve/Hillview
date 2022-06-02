<?php

namespace Drupal\asset_management\Plugin\Action;

use Drupal\views_bulk_operations\Action\ViewsBulkOperationsActionBase;
use Drupal\Core\Session\AccountInterface;
use Drupal\Core\StringTranslation\StringTranslationTrait;
use Drupal\node\Entity\Node;
use Drupal\Core\Access;


/**
 * Action description.
 *
 * @Action(
 *   id = "change_status_to_open",
 *   label = @Translation("Change status to open"),
 *   type = ""
 * )
 */
class UnblockAssetAction extends ViewsBulkOperationsActionBase {

  use StringTranslationTrait;

  /**
   * {@inheritdoc}
   */
  public function execute($entity = NULL) {
    // Do some processing..

    // Don't return anything for a default completion message, otherwise return translatable markup.
      if ($entity->getStatus() == 'blocked') {
        $entity->set('asset_status', 'open');
        $entity->save();
      }
    

}

  /**
   * {@inheritdoc}
   */
  public function access($entity, AccountInterface $account = NULL, $return_as_object = FALSE) {
    // if ($entity->getStatus() == 'open') {
    // //   $access = $entity->access('block', $account, TRUE);
    //     return new AccessResultAllowed();
    // //   return $access->isAllowed();
    // }
    // if($entity->getStatus() == 'blocked'){
    //     return new AccessResultAllowed();
    // }
    

    // Other entity types may have different
    // access methods and properties.
    return TRUE;
  }

//   public static function forbidden($reason = NULL) {
//     if($entity->getStatus() == 'open' || $entity->getStatus() == 'assigned')
//     {
//         assert('is_string($reason) || is_null($reason)');
//         return new AccessResultForbidden($reason);
//     }
//   }

}