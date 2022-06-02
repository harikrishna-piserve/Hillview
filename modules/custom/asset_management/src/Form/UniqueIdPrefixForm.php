<?php

namespace Drupal\asset_management\Form;

use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormStateInterface;

/**
 * Class UniqueIdPrefixForm.
 */
class UniqueIdPrefixForm extends ConfigFormBase {

  /**
   * {@inheritdoc}
   */
  protected function getEditableConfigNames() {
    return [
      'asset_management.uniqueidprefix',
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'unique_id_prefix_form';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $config = $this->config('asset_management.uniqueidprefix');
    $form['unique_id_prefix'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Unique Id Prefix'),
      '#description' => $this->t('Enter the prefix for the Unique Id'),
      '#maxlength' => 64,
      '#size' => 64,
      '#default_value' => $config->get('unique_id_prefix'),
    ];
    return parent::buildForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    parent::submitForm($form, $form_state);

    $this->config('asset_management.uniqueidprefix')
      ->set('unique_id_prefix', $form_state->getValue('unique_id_prefix'))
      ->save();
  }

}
