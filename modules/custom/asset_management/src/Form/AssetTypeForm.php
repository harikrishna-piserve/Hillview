<?php

namespace Drupal\asset_management\Form;

use Drupal\Core\Entity\EntityForm;
use Drupal\Core\Form\FormStateInterface;

/**
 * Class AssetTypeForm.
 */
class AssetTypeForm extends EntityForm {

  /**
   * {@inheritdoc}
   */
  public function form(array $form, FormStateInterface $form_state) {
    $form = parent::form($form, $form_state);

    $asset_type = $this->entity;
    $form['label'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Label'),
      '#maxlength' => 255,
      '#default_value' => $asset_type->label(),
      '#description' => $this->t("Label for the Asset type."),
      '#required' => TRUE,
    ];

    

    $form['id'] = [
      '#type' => 'machine_name',
      '#default_value' => $asset_type->id(),
      '#machine_name' => [
        'exists' => '\Drupal\asset_management\Entity\AssetType::load',
      ],
      '#disabled' => !$asset_type->isNew(),
    ];


    $form['prefixid'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Prefix Id'),
      '#maxlength' => 255,
      '#default_value' => $asset_type->getPrefixid(),
      '#description' => $this->t("Prefix Id for the Asset type."),
      '#required' => TRUE,
    ];

    $form['prefix'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Prefix'),
      '#maxlength' => 255,
      '#default_value' => $asset_type->getPrefix(),
      '#description' => $this->t("Prefix for the Asset type."),
      '#required' => TRUE,
    ];

    /* You will need additional form elements for your custom properties. */

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function save(array $form, FormStateInterface $form_state) {
    $asset_type = $this->entity;
    $status = $asset_type->save();

    switch ($status) {
      case SAVED_NEW:
        $this->messenger()->addMessage($this->t('Created the %label Asset type.', [
          '%label' => $asset_type->label(),
        ]));
        break;

      default:
        $this->messenger()->addMessage($this->t('Saved the %label Asset type.', [
          '%label' => $asset_type->label(),
        ]));
    }
    $form_state->setRedirectUrl($asset_type->toUrl('collection'));
  }

}
