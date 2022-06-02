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

class AssetCreateForm extends FormBase {
  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'create_asset_form';
  }
   /**
   * {@inheritdoc}
   */

  public function buildForm(array $form, FormStateInterface $form_state,$id = NULL) {


    $form['name'] = array(
        '#type'     => 'textfield',
        '#title' => t('Asset Name'),
    );

    $form['specification'] = array(
        '#type'     => 'textarea',
        '#title' => t('Specification'),
    );

    $form['asset_status'] = array(
        '#type'     => 'select',
        '#title' => t('Status'),
    );

    $form['asset_location'] = array(
        '#type'     => 'select',
        '#title' => t('Location'),
    );

    $form['asset_image'] = array(
        '#type' => 'managed_file',
        '#title' => 'Asset Image',
    );

    $form['asset_files'] = array(
        '#type'     => 'file',
        '#title'    => 'Asset documents',
    );

    $form['asset_forms'] = array(
        '#type'     => 'select',
        '#title'    => 'Asset related form',
    );
    
    $form['asset_submissions'] = array(
        '#type'     => 'select',
        '#title'    => 'Asset related Submissions',
    );

  
    $form['actions']['submit'] = array(
      '#type' => 'submit',
      '#value' => $this->t('Submit'),
      '#button_type' => 'primary',
    );

    //dpm($asset);
    
    return $form;
  }


   /**
   * {@inheritdoc}
   */

  public function submitForm(array &$form, FormStateInterface $form_state) {

    $values = $form_state->getValues();
    dpm($id);
    $entity = Asset::create([
    'name' => $form_state->getValue('name'),
    'specification' => $form_state->getValue('specification'),
    'asset_status' => $form_state->getValue('asset_status'),
    'asset_location' => $form_state->getValue('asset_location'),
    'asset_image' => $form_state->getValue('asset_image'),
    'asset_files' => $form_state->getValue('asset_files'),
    'asset_forms' => $form_state->getValue('asset_forms'),
    'asset_submissions' => $form_state->getValue('asset_submissions'),
    ]);

    $entity->save();
    \Drupal::messenger()->addMessage(t('Proceed to to next!'),'success');
    $form_state->setRedirect('asset_management.asset_list');
    }
}