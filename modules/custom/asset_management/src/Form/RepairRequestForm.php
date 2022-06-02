<?php

namespace Drupal\asset_management\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\asset_management\Entity\Assignation;
use Drupal\asset_management\Entity\MaintenanceRecord;

/**
 * Class RepairRequestForm.
 */
class RepairRequestForm extends FormBase {

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'repair_request_form';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    

    $form['asset_name'] = array(
      '#type' => 'select',
      '#title' => t('Asset Name:'),
      '#options' => $options,
    );

    $form['comments'] = array(
      '#type' => 'textarea',
      '#title' => t('Comments'),
      '#required' => TRUE,
    );

    $form['submit'] = [
      '#type' => 'submit',
      '#value' => $this->t('Submit'),
    ];

    return $form;
  }

  // /**
  //  * {@inheritdoc}
  //  */
  // public function validateForm(array &$form, FormStateInterface $form_state) {
  //   foreach ($form_state->getValues() as $key => $value) {
  //     // @TODO: Validate fields.
  //   }
  //   parent::validateForm($form, $form_state);
  // }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    // Display result.
      $current_user = \Drupal::currentUser();

      $entity = MaintenanceRecord::create([
      'name' => $form_state->getValue('asset_name'),
      'maintenance_type' => 'repair',
      'field_comments' => $form_state->getValue('comments'),
      'user_id' => $current_user->id(),
      ]);

      $entity->save();
      \Drupal::messenger()->addMessage(t('Your application is being submitted!'),'success');
      $form_state->setRedirect('asset_management.assignee_view');

  }

}
