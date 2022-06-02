<?php
/**
 * @file
 * Contains \Drupal\asset_management\Form\AssetBlockForm.
 */
namespace Drupal\asset_management\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\asset_management\Entity\Asset;
use Drupal\Core\Url;
use Drupal\Core\Render\Element;

class AssetBlockForm extends FormBase {
  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'asset_block';
  }
   /**
   * {@inheritdoc}
   */
  //public $id;

  public function buildForm(array $form, FormStateInterface $form_state,$id = NULL) {


    dpm($id);
    $form['entity_id'] = [
      '#type' =>'hidden',
      '#value' => $id,
    ];

  
    $form['actions']['submit'] = array(
      '#type' => 'submit',
      '#value' => $this->t('Block'),
      '#button_type' => 'primary',
    );
    //dpm($asset);
    
    return $form;
  }


   /**
   * {@inheritdoc}
   */

  public function submitForm(array &$form, FormStateInterface $form_state) {

    // $fields  = [
    //   'asset_status'=> 'blocked',
    // ];
    // $query = \Drupal::database();
    //    $query->update('asset_field_data')
    //                ->condition('id',$asset)
    //                ->fields($fields)
    //               ->execute();
    //       drupal_set_message("Asset Blocked");
    //      $form_state->setRedirect('asset_management.asset_list');
    $id = $form_state->getValues();
    dpm($id);
    $entity = Asset::load($id['entity_id']);

    dpm($entity);
    //set value for field

    
    $entity->asset_status->value = 'blocked';
    //field tag
    $entity->save();
    drupal_set_message("Asset Blocked");
    $form_state->setRedirect('asset_management.asset_list');
    }
}