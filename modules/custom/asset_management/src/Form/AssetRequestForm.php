<?php
/**
 * @file
 * Contains \Drupal\asset_management\Form\AssetRequestForm.
 */
namespace Drupal\asset_management\Form;
use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\asset_management\Entity\Assignation;

class AssetRequestForm extends FormBase {
  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'request_form';
  }
   /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $form['asset_type'] = array(
      '#type' => 'select',
      '#title' => t('Asset Type:'),
      '#options' => $options,
    );
    $form['request_comment'] = array(
      '#type' => 'textarea',
      '#title' => t('Comments:'),
      '#required' => TRUE,
    );

    // $current_user = \Drupal::currentUser();
    // $form['requestuser_id']= array(
    //     '#type' => 'textfield',
    //     '#value' => $current_user->id(),
    //     '#access' => 0,
    //   );
    // $user_id = $current_user->id();
    // $form['actions']['#type'] = 'actions';
    $form['actions']['submit'] = array(
      '#type' => 'submit',
      '#value' => $this->t('Save'),
      '#button_type' => 'primary',
    );
    
    return $form;
  }


   /**
   * {@inheritdoc}
   */

  public function submitForm(array &$form, FormStateInterface $form_state) {
    // drupal_set_message($this->t('@can_name ,Your application is being submitted!', array('@can_name' => $form_state->getValue('candidate_name'))));
    //  foreach ($form_state->getValues() as $key => $value) {
    //    drupal_set_message($key . ': ' . $value);
    //  }

    $current_user = \Drupal::currentUser();

    $entity = Assignation::create([
    'asset_type' => $form_state->getValue('asset_type'),
    'request_comment' => $form_state->getValue('request_comment'),
    'assignee' => $current_user->id(),
    'assignation_flag'=> 1,
    ]);

    $entity->save();
    \Drupal::messenger()->addMessage(t('Your application is being submitted!'),'success');
    $form_state->setRedirect('asset_management.assignee_view');
  }
}