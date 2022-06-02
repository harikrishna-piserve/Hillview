<?php

namespace Drupal\asset_management\Controller;

use Drupal\Component\Utility\Xss;
use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\DependencyInjection\ContainerInjectionInterface;
use Drupal\Core\Url;
use Drupal\asset_management\Entity\AssetInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Class AssetBlockController.
 */
class AssetBlockController extends ControllerBase {

  /**
   * Hello.
   *
   * @return string
   *   Return Hello string.
   */
  
  public function hello($id) {
    dpm($id);
    $fields = [
      'asset_status' => 'blocked',
    ];
    $query = \Drupal::database();
       $query->update('asset_field_data')
                   ->condition('id',$id)
                   ->fields($fields)
                  ->execute();
             drupal_set_message("succesfully blocked");
            $form_state->setRedirect('asset_management.asset-view');
    // return [
    //   '#type' => 'markup',
    //   '#markup' => $this->t('Implement method: hello with parameter(s): $asset'),
    // ];
  }

}
