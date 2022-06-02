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

/**
 * Defines the Maintenance record entity.
 *
 * @ingroup asset_management
 *
 * @ContentEntityType(
 *   id = "maintenance_record",
 *   label = @Translation("Maintenance record"),
 *   handlers = {
 *     "storage" = "Drupal\asset_management\MaintenanceRecordStorage",
 *     "view_builder" = "Drupal\Core\Entity\EntityViewBuilder",
 *     "list_builder" = "Drupal\asset_management\MaintenanceRecordListBuilder",
 *     "views_data" = "Drupal\asset_management\Entity\MaintenanceRecordViewsData",
 *     "translation" = "Drupal\asset_management\MaintenanceRecordTranslationHandler",
 *
 *     "form" = {
 *       "default" = "Drupal\asset_management\Form\MaintenanceRecordForm",
 *       "add" = "Drupal\asset_management\Form\MaintenanceRecordForm",
 *       "edit" = "Drupal\asset_management\Form\MaintenanceRecordForm",
 *       "delete" = "Drupal\asset_management\Form\MaintenanceRecordDeleteForm",
 *     },
 *     "route_provider" = {
 *       "html" = "Drupal\asset_management\MaintenanceRecordHtmlRouteProvider",
 *     },
 *     "access" = "Drupal\asset_management\MaintenanceRecordAccessControlHandler",
 *   },
 *   base_table = "maintenance_record",
 *   data_table = "maintenance_record_field_data",
 *   revision_table = "maintenance_record_revision",
 *   revision_data_table = "maintenance_record_field_revision",
 *   translatable = TRUE,
 *   admin_permission = "administer maintenance record entities",
 *   entity_keys = {
 *     "id" = "id",
 *     "revision" = "vid",
 *     "label" = "name",
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
 *     "canonical" = "/maintenance_record/{maintenance_record}",
 *     "add-form" = "/maintenance_record/add",
 *     "edit-form" = "/maintenance_record/{maintenance_record}/edit",
 *     "delete-form" = "/admin/structure/maintenance_record/{maintenance_record}/delete",
 *     "version-history" = "/admin/structure/maintenance_record/{maintenance_record}/revisions",
 *     "revision" = "/admin/structure/maintenance_record/{maintenance_record}/revisions/{maintenance_record_revision}/view",
 *     "revision_revert" = "/admin/structure/maintenance_record/{maintenance_record}/revisions/{maintenance_record_revision}/revert",
 *     "revision_delete" = "/admin/structure/maintenance_record/{maintenance_record}/revisions/{maintenance_record_revision}/delete",
 *     "translation_revert" = "/admin/structure/maintenance_record/{maintenance_record}/revisions/{maintenance_record_revision}/revert/{langcode}",
 *     "collection" = "/admin/structure/maintenance_record",
 *   },
 *   field_ui_base_route = "maintenance_record.settings"
 * )
 */
class MaintenanceRecord extends EditorialContentEntityBase implements MaintenanceRecordInterface {

  use EntityChangedTrait;
  use EntityPublishedTrait;

  /**
   * {@inheritdoc}
   */
  public static function preCreate(EntityStorageInterface $storage_controller, array &$values) {
    parent::preCreate($storage_controller, $values);
    $values += [
      'user_id' => \Drupal::currentUser()->id(),
      'maintenance_status' => 'open',
      'maintenance_type' => 'maintenance',
    ];
  }

  /**
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
    // make the maintenance_record owner the revision author.
    if (!$this->getRevisionUser()) {
      $this->setRevisionUserId($this->getOwnerId());
    }
  }

  /**
   * {@inheritdoc}
   */
  public function getName() {
    return $this->get('name')->value;
  }

  /**
   * {@inheritdoc}
   */
  public function setName($name) {
    $this->set('name', $name);
    return $this;
  }

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
      ->setDescription(t('The user ID of author of the Maintenance record entity.'))
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

    $fields['name'] = BaseFieldDefinition::create('entity_reference')
      ->setLabel(t('Asset Name'))
      ->setDescription(t('The name of the Asset.'))
      ->setRevisionable(TRUE)
      ->setSetting('target_type', 'asset')
      ->setSettings([
        'max_length' => 50,
        'text_processing' => 0,
      ])
      ->setSetting('handler', 'default')
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
      ->setDisplayConfigurable('view', TRUE)
      ->setRequired(TRUE);

    $fields['vendor'] = BaseFieldDefinition::create('entity_reference')
      ->setLabel(t('Vendor'))
      ->setDescription(t('Select the vendor'))
      ->setRevisionable(TRUE)
      ->setSetting('target_type', 'vendor_record')
      ->setTranslatable(TRUE)
      ->setDisplayOptions('view', [
        'label' => 'hidden',
        'type' => 'author',
        'weight' => 0,
      ])
      ->setSetting('handler', 'default')
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

    $fields['maintenance_type'] = BaseFieldDefinition::create('list_string')
      ->setLabel(t('type'))
      ->setDescription(t('Maintenance Type'))
      ->setReadOnly(TRUE)
      ->setRevisionable(TRUE)

      ->setSettings([
        'allowed_values' => [
          'maintenance' => "Maintenance",
          'repair' => "Repair",
        ],
      ])
      
      ->setRequired(TRUE)
      ->setCardinality(1)
      ->setDisplayOptions('form',[
      'type' => 'options_buttons',
      'weight' => -4,
      ])
      ->setDisplayConfigurable('view', TRUE)
      ->setDisplayConfigurable('form', TRUE)
      ->setTranslatable(TRUE);

      $fields['maintenance_status'] = BaseFieldDefinition::create('list_string')
      ->setLabel(t('Maintenance Status'))
      ->setDescription(t('Maintenance Status'))
      ->setReadOnly(TRUE)
      ->setRevisionable(TRUE)
      
      ->setSettings([
      'allowed_values'=>[
      'open'=>"Open",
      'assigned'=>"Assigned",
      'returned'=>"Returned",
      ]
      ])
      
      ->setRequired(TRUE)
      ->setCardinality(1)
      ->setDisplayOptions('form',[
      'type' => 'options_buttons',
      'weight' => -4,
      ])
      ->setDisplayConfigurable('view', TRUE)
      ->setDisplayConfigurable('form', TRUE)
      ->setTranslatable(TRUE);


    $fields['status']->setDescription(t('A boolean indicating whether the Maintenance record is published.'))
      ->setDisplayOptions('form', [
        'type' => 'boolean_checkbox',
        'weight' => -3,
      ]);

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
