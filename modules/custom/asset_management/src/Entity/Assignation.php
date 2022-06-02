<?php

namespace Drupal\asset_management\Entity;

use Drupal\Core\Entity\EntityStorageInterface;
use Drupal\Core\Field\BaseFieldDefinition;
use Drupal\Core\Entity\EditorialContentEntityBase;
use Drupal\Core\Entity\RevisionableInterface;
use Drupal\Core\Entity\EntityChangedTrait;
use Drupal\Core\Entity\EntityPublishedTrait;
use Drupal\Core\Entity\EntityTypeInterface;
use Drupal\user\UserInterface;
use Drupal\Core\Routing\RouteMatchInterface;
use Drupal\Core\Ajax\AjaxResponse;
use Drupal\Core\Ajax\HtmlCommand;

/**
 * Defines the Assignation entity.
 *
 * @ingroup asset_management
 *
 * @ContentEntityType(
 *   id = "assignation",
 *   label = @Translation("Assignation"),
 *   handlers = {
 *     "storage" = "Drupal\asset_management\AssignationStorage",
 *     "view_builder" = "Drupal\Core\Entity\EntityViewBuilder",
 *     "list_builder" = "Drupal\asset_management\AssignationListBuilder",
 *     "views_data" = "Drupal\asset_management\Entity\AssignationViewsData",
 *     "translation" = "Drupal\asset_management\AssignationTranslationHandler",
 *
 *     "form" = {
 *       "default" = "Drupal\asset_management\Form\AssignationForm",
 *       "add" = "Drupal\asset_management\Form\AssignationForm",
 *       "edit" = "Drupal\asset_management\Form\AssignationForm",
 *       "delete" = "Drupal\asset_management\Form\AssignationDeleteForm",
 *     },
 *     "route_provider" = {
 *       "html" = "Drupal\asset_management\AssignationHtmlRouteProvider",
 *     },
 *     "access" = "Drupal\asset_management\AssignationAccessControlHandler",
 *   },
 *   base_table = "assignation",
 *   data_table = "assignation_field_data",
 *   revision_table = "assignation_revision",
 *   revision_data_table = "assignation_field_revision",
 *   translatable = TRUE,
 *   admin_permission = "administer assignation entities",
 *   entity_keys = {
 *     "id" = "id",
 *     "revision" = "vid",
 *     "uuid" = "uuid",
 *     "uid" = "user_id",
 *     "langcode" = "langcode",
 *     "published" = "status",
 *   },
 *   revision_metadata_keys = {
 *     "revision_user" = "revision_user",
 *     "revision_created" = "revision_created",
 *     "revision_log_message" = "revision_log_message",
 *   },
 *   links = {
 *     "canonical" = "/assignation/{assignation}",
 *     "add-form" = "/assignation/add",
 *     "edit-form" = "/admin/structure/assignation/{assignation}/edit",
 *     "delete-form" = "/admin/structure/assignation/{assignation}/delete",
 *     "version-history" = "/admin/structure/assignation/{assignation}/revisions",
 *     "revision" = "/admin/structure/assignation/{assignation}/revisions/{assignation_revision}/view",
 *     "revision_revert" = "/admin/structure/assignation/{assignation}/revisions/{assignation_revision}/revert",
 *     "revision_delete" = "/admin/structure/assignation/{assignation}/revisions/{assignation_revision}/delete",
 *     "translation_revert" = "/admin/structure/assignation/{assignation}/revisions/{assignation_revision}/revert/{langcode}",
 *     "collection" = "/admin/structure/assignation",
 *   },
 *   field_ui_base_route = "assignation.settings"
 * )
 */
class Assignation extends EditorialContentEntityBase implements AssignationInterface {

  use EntityChangedTrait;
  use EntityPublishedTrait;

  /**
   * {@inheritdoc}
   */
  public static function preCreate(EntityStorageInterface $storage_controller, array &$values) {
    parent::preCreate($storage_controller, $values);
    
    $values += [
      'user_id' => \Drupal::currentUser()->id(),
      // 'asset_type' => $bundle,
      'assignation_status'=>'in_progress',
    ];
  }

  /*
   * {@inheritdoc}
   */
  protected function urlRouteParameters($rel) {
    $uri_route_parameters = parent::urlRouteParameters($rel);

    if ($rel === 'revision_revert' && $this instanceof RevisionableInterface) {
      $uri_route_parameters[$this->getEntityTypeId() . '_revision'] = $this->getRevisionId();
    }
    elseif ($rel === 'revision_delete' && $this instanceof RevisionableInterface) {
      $uri_route_parameters[$this->getEntityTypeId() . '_revision'] = $this->getRevisionId();
    }

    return $uri_route_parameters;
  }

  /**
   * {@inheritdoc}
   */
  public function preSave(EntityStorageInterface $storage) {
    parent::preSave($storage);

    foreach (array_keys($this->getTranslationLanguages()) as $langcode) {
      $translation = $this->getTranslation($langcode);

      // If no owner has been set explicitly, make the anonymous user the owner.
      if (!$translation->getOwner()) {
        $translation->setOwnerId(0);
      }
    }

    // If no revision author has been set explicitly,
    // make the assignation owner the revision author.
    if (!$this->getRevisionUser()) {
      $this->setRevisionUserId($this->getOwnerId());
    }

  }

  // /**
  //  * {@inheritdoc}
  //  */
  // public function getName() {
  //   return $this->get('name')->value;
  // }

  // /**
  //  * {@inheritdoc}
  //  */
  // public function setName($name) {
  //   $this->set('name', $name);
  //   return $this;
  // }

  /**
   * {@inheritdoc}
   */
  public function getCreatedTime() {
    return $this->get('created')->value;
  }

  /**
   * {@inheritdoc}
   */
  public function setCreatedTime($timestamp) {
    $this->set('created', $timestamp);
    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public function getOwner() {
    return $this->get('user_id')->entity;
  }

  /**
   * {@inheritdoc}
   */
  public function getOwnerId() {
    return $this->get('user_id')->target_id;
  }

  /**
   * {@inheritdoc}
   */
  public function setOwnerId($uid) {
    $this->set('user_id', $uid);
    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public function getStatus() {
    return $this->get('assignation_status')->getString();
  }

  /**
   * {@inheritdoc}
   */
  public function setStatus($status) {
    $this->set('assignation_status', $status);
    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public function setAssetStatus($status) {
    if(!empty($this->asset_id->entity))
    {
      $this->asset_id->entity->setStatus($status);
    
      // kint($status);
      $this->asset_id->entity->save();
    }
      return $this;
    
  }


  /**
   * {@inheritdoc}
   */
  public function setOwner(UserInterface $account) {
    $this->set('user_id', $account->id());
    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public static function baseFieldDefinitions(EntityTypeInterface $entity_type) {
    $fields = parent::baseFieldDefinitions($entity_type);

    // Add the published field.
    $fields += static::publishedBaseFieldDefinitions($entity_type);

    $fields['user_id'] = BaseFieldDefinition::create('entity_reference')
      ->setLabel(t('Authored by'))
      ->setDescription(t('The user ID of author of the Assignation entity.'))
      ->setRevisionable(TRUE)
      ->setSetting('target_type', 'user')
      ->setSetting('handler', 'default')
      ->setTranslatable(TRUE)
      ->setDisplayOptions('view', [
        'label' => 'hidden',
        'type' => 'author',
        'weight' => 0,
      ])
      ->setDisplayOptions('form', [
        'type' => 'entity_reference_autocomplete',
        'weight' => 5,
        'settings' => [
          'match_operator' => 'CONTAINS',
          'size' => '60',
          'autocomplete_type' => 'tags',
          'placeholder' => '',
        ],
      ])
      ->setDisplayConfigurable('form', TRUE)
      ->setDisplayConfigurable('view', TRUE);

      



      $fields['asset_id'] = BaseFieldDefinition::create('entity_reference')
      ->setLabel(t('Asset'))
      ->setDescription(t('Asset id'))
      ->setRevisionable(TRUE)
      ->setSetting('target_type', 'asset')
      ->setTranslatable(TRUE)
      ->setDisplayOptions('view', [
        'label' => 'hidden',
        'type' => 'author',
        'weight' => 0,
      ])
      ->setSetting('handler', 'default')
      // ->setSetting('handler_settings', [
      //   'view' => [
      //     'view_name' => 'available_assets',
      //     'display_name' => 'available_assets_reference',
      //   ],
      //   // 'view' => [
      //   //   'view_name' => 'closed_assets',
      //   //   'display_name' => 'closed_assets_reference',
      //   // ],
      // ])
      // ->setSettings([
      //   'allowed_values'=> $assets
      //   ])
      ->setDisplayOptions('form', [
        'type' => 'entity_reference_autocomplete',
        'weight' => 5,
        'settings' => [
          'match_operator' => 'CONTAINS',
          'size' => '60',
          'autocomplete_type' => 'tags',
          'placeholder' => '',
        ],
      ])
      ->setDisplayConfigurable('form', TRUE)
      ->setDisplayConfigurable('view', TRUE);


      $fields['assignee'] = BaseFieldDefinition::create('entity_reference')
      ->setLabel(t('Assigned to'))
      ->setDescription(t('To whom were asset assigned to.'))
      ->setRevisionable(TRUE)
      ->setSetting('target_type', 'user')
      ->setSetting('handler', 'default')
      ->setTranslatable(TRUE)
      ->setDisplayOptions('view', [
        'label' => 'hidden',
        'type' => 'author',
        'weight' => 0,
      ])
      ->setDisplayOptions('form', [
        'type' => 'entity_reference_autocomplete',
        'weight' => 5,
        'settings' => [
          'match_operator' => 'CONTAINS',
          'size' => '60',
          'autocomplete_type' => 'tags',
          'placeholder' => '',
        ],
      ])
      ->setDisplayConfigurable('form', TRUE)
      ->setDisplayConfigurable('view', TRUE);

    $fields['status']->setDescription(t('A boolean indicating whether the Assignation is published.'))
      ->setDisplayOptions('form', [
        'type' => 'boolean_checkbox',
        'weight' => -3,
      ]);


      $fields['assignation_status'] = BaseFieldDefinition::create('list_string')
      ->setLabel(t('Assignation Status'))
      ->setDescription(t('Asset Status'))
      ->setReadOnly(TRUE)
      ->setRevisionable(TRUE)
      
      ->setSettings([
      'allowed_values'=>[
      'assigned'=>"Assigned",
      'closed'=>"Closed",
      'in_progress'=>"In Progress"
      ]
      ])
      
      ->setRequired(TRUE)
      ->setCardinality(1)
      ->setDisplayOptions('form',[
      'type' => 'options_buttons',
      'weight' => -4,
      ])
      ->setDisplayConfigurable('form', TRUE)
      ->setTranslatable(TRUE);

      // $fields['asset_type'] = BaseFieldDefinition::create('string')
      // ->setLabel(t('Asset Type'))
      // ->setDescription(t('Asset Type.'))
      // ->setRevisionable(TRUE)
      // ->setSettings([
      // 'max_length' => 50,
      // 'text_processing' => 0,
      // ])
      // ->setDefaultValue('')
      // ->setDisplayOptions('view', [
      // 'label' => 'above',
      // 'type' => 'string',
      // 'weight' => -4,
      // ])
      // ->setDisplayOptions('form', [
      // 'type' => 'string_textfield',
      // 'weight' => -4,
      // ])
      // ->setDisplayConfigurable('form', TRUE)
      // ->setDisplayConfigurable('view', TRUE)
      // ->setRequired(TRUE);

      $fields['asset_type'] = BaseFieldDefinition::create('entity_reference')
      ->setLabel(t('Asset Type'))
      ->setDescription(t('Asset Type'))
      ->setRevisionable(TRUE)
      ->setSetting('target_type', 'asset_type')
      ->setTranslatable(TRUE)
      ->setDisplayOptions('view', [
        'label' => 'hidden',
        'type' => 'author',
        'weight' => 0,
      ])
      ->setSetting('handler', 'default')
      // ->setSetting('handler_settings', [
      //   'view' => [
      //     'view_name' => 'available_assets',
      //     'display_name' => 'available_assets_reference',
      //   ],
      //   // 'view' => [
      //   //   'view_name' => 'closed_assets',
      //   //   'display_name' => 'closed_assets_reference',
      //   // ],
      // ])
      // ->setSettings([
      //   'allowed_values'=> $assets
      //   ])
      ->setDisplayOptions('form', [
        'type' => 'entity_reference_list',
        'weight' => 5,
        'settings' => [
          'match_operator' => 'CONTAINS',
          'size' => '60',
          'autocomplete_type' => 'tags',
          'placeholder' => '',
        ],
      ])
      ->setDisplayConfigurable('form', TRUE)
      ->setDisplayConfigurable('view', TRUE);

      $fields['request_comment'] = BaseFieldDefinition::create('string')
      ->setLabel(t('Request Comment'))
      ->setDescription(t('Request Comment.'))
      ->setRevisionable(TRUE)
      ->setSettings([
      'max_length' => 250,
      'text_processing' => 0,
      ])
      ->setDefaultValue('')
      ->setDisplayOptions('view', [
      'label' => 'above',
      'type' => 'string',
      'weight' => -4,
      ])
      ->setDisplayOptions('form', [
      'type' => 'string_textfield',
      'weight' => -4,
      ])
      ->setDisplayConfigurable('form', TRUE)
      ->setDisplayConfigurable('view', TRUE)
      ->setRequired(FALSE);

      $fields['assignation_flag'] = BaseFieldDefinition::create('string')
      ->setRevisionable(TRUE)
      ->setSettings([
      'max_length' => 50,
      'text_processing' => 0,
      ])
      ->setDefaultValue('0')
      ->setDisplayOptions('view', [
      'label' => 'above',
      'type' => 'string',
      'weight' => -4,
      ])
      ->setDisplayOptions('form', [
      'type' => 'string_textfield',
      'weight' => -4,
      ])
      ->setDisplayConfigurable('form', FALSE)
      ->setDisplayConfigurable('view', FALSE)
      ->setReadOnly(TRUE)
      ->setRequired(FALSE);


    $fields['created'] = BaseFieldDefinition::create('created')
      ->setLabel(t('Created'))
      ->setDescription(t('The time that the entity was created.'));

    $fields['changed'] = BaseFieldDefinition::create('changed')
      ->setLabel(t('Changed'))
      ->setDescription(t('The time that the entity was last edited.'));

    $fields['revision_translation_affected'] = BaseFieldDefinition::create('boolean')
      ->setLabel(t('Revision translation affected'))
      ->setDescription(t('Indicates if the last edit of a translation belongs to current revision.'))
      ->setReadOnly(TRUE)
      ->setRevisionable(TRUE)
      ->setTranslatable(TRUE);

    return $fields;
  }

}
