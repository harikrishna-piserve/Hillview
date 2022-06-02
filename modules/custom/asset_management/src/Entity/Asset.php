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
use Drupal\Core\Field\FieldStorageDefinitionInterface;

/**
 * Defines the Asset entity.
 *
 * @ingroup asset_management
 *
 * @ContentEntityType(
 *   id = "asset",
 *   label = @Translation("Asset"),
 *   bundle_label = @Translation("Asset type"),
 *   handlers = {
 *     "storage" = "Drupal\asset_management\AssetStorage",
 *     "view_builder" = "Drupal\Core\Entity\EntityViewBuilder",
 *     "list_builder" = "Drupal\asset_management\AssetListBuilder",
 *     "views_data" = "Drupal\asset_management\Entity\AssetViewsData",
 *     "translation" = "Drupal\asset_management\AssetTranslationHandler",
 *
 *     "form" = {
 *       "default" = "Drupal\asset_management\Form\AssetForm",
 *       "add" = "Drupal\asset_management\Form\AssetForm",
 *       "edit" = "Drupal\asset_management\Form\AssetForm",
 *       "delete" = "Drupal\asset_management\Form\AssetDeleteForm",
 *     },
 *     "route_provider" = {
 *       "html" = "Drupal\asset_management\AssetHtmlRouteProvider",
 *     },
 *     "access" = "Drupal\asset_management\AssetAccessControlHandler",
 *   },
 *   revision_metadata_keys = {
 *     "revision_user" = "revision_user",
 *     "revision_created" = "revision_created",
 *     "revision_log_message" = "revision_log_message",
 *   },
 *   base_table = "asset",
 *   data_table = "asset_field_data",
 *   revision_table = "asset_revision",
 *   revision_data_table = "asset_field_revision",
 *   translatable = TRUE,
 *   permission_granularity = "bundle",
 *   admin_permission = "administer asset entities",
 *   entity_keys = {
 *     "id" = "id",
 *     "revision" = "vid",
 *     "bundle" = "type",
 *     "label" = "name",
 *     "uuid" = "uuid",
 *     "uid" = "user_id",
 *     "langcode" = "langcode",
 *     "published" = "status",
 *   },
 *   links = {
 *     "canonical" = "/asset/{asset}",
 *     "add-page" = "/asset/add",
 *     "add-form" = "/asset/add/{asset_type}",
 *     "edit-form" = "/asset/{asset}/edit",
 *     "delete-form" = "/admin/structure/asset/{asset}/delete",
 *     "version-history" = "/admin/structure/asset/{asset}/revisions",
 *     "revision" = "/admin/structure/asset/{asset}/revisions/{asset_revision}/view",
 *     "revision_revert" = "/admin/structure/asset/{asset}/revisions/{asset_revision}/revert",
 *     "revision_delete" = "/admin/structure/asset/{asset}/revisions/{asset_revision}/delete",
 *     "translation_revert" = "/admin/structure/asset/{asset}/revisions/{asset_revision}/revert/{langcode}",
 *     "collection" = "/admin/structure/asset",
 *   },
 *   bundle_entity_type = "asset_type",
 *   field_ui_base_route = "entity.asset_type.edit_form"
 * )
 */
class Asset extends EditorialContentEntityBase implements AssetInterface {

  use EntityChangedTrait;
  use EntityPublishedTrait;

  /**
   * {@inheritdoc}
   */
  public static function preCreate(EntityStorageInterface $storage_controller, array &$values) {
    parent::preCreate($storage_controller, $values);
    $values += [
      'user_id' => \Drupal::currentUser()->id(),
      'asset_status' => 'open',
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

    $bundle = $this->bundle();
    $prefix = \Drupal::config('asset_management.asset_type.' . $bundle)->get('prefix');
    $currentindex = \Drupal::config('asset_management.asset_type.' . $bundle)->get('prefixid');
    $new_assetid = $prefix . $currentindex;
    $this->set('asset_id', $new_assetid);
    $nextindex = $currentindex + 1;
    // $this->set('asset_type', $bundle);
    \Drupal::service('config.factory')->getEditable('asset_management.asset_type.' . $bundle)->set('prefixid', $nextindex)->save();
    foreach (array_keys($this->getTranslationLanguages()) as $langcode) {
      $translation = $this->getTranslation($langcode);

      // If no owner has been set explicitly, make the anonymous user the owner.
      if (!$translation->getOwner()) {
        $translation->setOwnerId(0);
      }
    }

    // If no revision author has been set explicitly,
    // make the asset owner the revision author.
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
  public function getStatus() {
    return $this->get('asset_status')->getString();
  }

  /**
   * {@inheritdoc}
   */
  public function setStatus($status) {
    $this->set('asset_status', $status);
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
      ->setDescription(t('The user ID of author of the Asset entity.'))
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

    $fields['asset_id'] = BaseFieldDefinition::create('string')
      ->setLabel(t('Asset Id'))
      ->setDescription(t('The specification of the Asset entity.'))
      ->setRevisionable(TRUE)
      ->setSettings([
        'max_length' => 250,
        'text_processing' => 0,
      ])
      ->setDisplayOptions('view', [
        'label' => 'above',
        'type' => 'string',
        'weight' => -4,
      ])

      ->setDisplayConfigurable('view', FALSE)
      ->setReadOnly(TRUE)
      ->setRequired(FALSE);

    $fields['name'] = BaseFieldDefinition::create('string')
      ->setLabel(t('Name'))
      ->setDescription(t('The name of the Asset entity.'))
      ->setRevisionable(TRUE)
      ->setSettings([
        'max_length' => 50,
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
      ->setRequired(TRUE);

    $fields['specification'] = BaseFieldDefinition::create('string')
      ->setLabel(t('Specification'))
      ->setDescription(t('The specification of the Asset entity.'))
      ->setRevisionable(TRUE)
      ->setSettings([
        'max_length' => 50,
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
      ->setRequired(TRUE);

    $fields['asset_status'] = BaseFieldDefinition::create('list_string')
      ->setLabel(t('Asset Status'))
      ->setDescription(t('Asset Status'))
      ->setReadOnly(TRUE)
      ->setRevisionable(TRUE)

      ->setSettings([
        'allowed_values' => [
          'open' => "Open",
          'assigned' => "Assigned",
          'blocked' => "Blocked",
        ],
      ])

      ->setRequired(TRUE)
      ->setCardinality(1)
      ->setDisplayOptions('form', [
        'type' => 'options_buttons',
        'weight' => -4,
      ])
      ->setDisplayConfigurable('view', TRUE)
      ->setDisplayConfigurable('form', TRUE)
      ->setTranslatable(TRUE);
    $fields['asset_location'] = BaseFieldDefinition::create('entity_reference')
      ->setLabel(t('Assigned Location'))
      ->setDescription(t('The assigned location ID of the Asset entity.'))
      ->setRevisionable(TRUE)
      ->setSetting('target_type', 'taxonomy_term')
      ->setSetting('handler', 'default')
      ->setTranslatable(TRUE)
      ->setDisplayOptions('view', [
        'label' => 'above',
        'type' => 'string',
        'weight' => 10,
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
    $fields['asset_image'] = BaseFieldDefinition::create('image')
      ->setLabel(t('Asset Image'))
      ->setDescription(t('Asset Image.'))
      ->setSettings([
        'alt_field_required' => FALSE,
        'file_extensions' => 'png jpg jpeg',
      ])
      ->setDisplayOptions('view', [
        'label' => 'hidden',
        'type' => 'default',
        'weight' => 0,
      ])
      ->setDisplayOptions('form', [
        'label' => 'hidden',
        'type' => 'image_image',
        'weight' => 0,
      ])
      ->setDisplayConfigurable('form', TRUE)
      ->setDisplayConfigurable('view', TRUE);
    $fields['asset_files'] = BaseFieldDefinition::create('file')
      ->setLabel(t('Asset documents'))
      ->setDescription(t('Asset documents.'))
      ->setSettings([
        'uri_scheme' => 'public',
        'file_extensions' => 'png jpg jpeg svg pdf xlsx csv doc docx mp3 mp4 vob mkv',
      ])
      ->setCardinality(FieldStorageDefinitionInterface::CARDINALITY_UNLIMITED)
      ->setDisplayOptions('view',
        [
          'label' => 'above',
          'type' => 'file',
          'weight' => -3,
        ]
      )
      ->setDisplayOptions('form', [
        'type' => 'file',
        'description' => [
          'theme' => 'file_upload_help',
          'description' => t('A Gettext Portable Object file.'),
        ],
        'weight' => -3,
      ])
      ->setDisplayConfigurable('form', TRUE)
      ->setDisplayConfigurable('view', TRUE);

    $fields['asset_forms'] = BaseFieldDefinition::create('entity_reference')
      ->setLabel(t('Asset related form'))
      ->setDescription(t('Asset related forms.'))
      ->setRevisionable(TRUE)
      ->setSetting('target_type', 'webform')
      ->setCardinality(FieldStorageDefinitionInterface::CARDINALITY_UNLIMITED)
      ->setSetting('handler', 'default')
      ->setTranslatable(TRUE)
      ->setDisplayOptions('view', [
        'label' => 'above',
        'type' => 'string',
        'weight' => 10,
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
    $fields['asset_submissions'] = BaseFieldDefinition::create('entity_reference')
      ->setLabel(t('Asset related Submissions'))
      ->setDescription(t('Asset related forms.'))
      ->setRevisionable(TRUE)
      ->setSetting('target_type', 'webform_submission')
      ->setCardinality(FieldStorageDefinitionInterface::CARDINALITY_UNLIMITED)
      ->setSetting('handler', 'default')
      ->setTranslatable(TRUE)
      ->setDisplayOptions('view', [
        'label' => 'above',
        'type' => 'string',
        'weight' => 10,
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

    $fields['status']->setDescription(t('A boolean indicating whether the Asset is published.'))
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
