<?php

namespace Drupal\asset_management;

use Drupal\Core\Entity\Sql\SqlContentEntityStorage;
use Drupal\Core\Session\AccountInterface;
use Drupal\Core\Language\LanguageInterface;
use Drupal\asset_management\Entity\MaintenanceRecordInterface;

/**
 * Defines the storage handler class for Maintenance record entities.
 *
 * This extends the base storage class, adding required special handling for
 * Maintenance record entities.
 *
 * @ingroup asset_management
 */
class MaintenanceRecordStorage extends SqlContentEntityStorage implements MaintenanceRecordStorageInterface {

  /**
   * {@inheritdoc}
   */
  public function revisionIds(MaintenanceRecordInterface $entity) {
    return $this->database->query(
      'SELECT vid FROM {maintenance_record_revision} WHERE id=:id ORDER BY vid',
      [':id' => $entity->id()]
    )->fetchCol();
  }

  /**
   * {@inheritdoc}
   */
  public function userRevisionIds(AccountInterface $account) {
    return $this->database->query(
      'SELECT vid FROM {maintenance_record_field_revision} WHERE uid = :uid ORDER BY vid',
      [':uid' => $account->id()]
    )->fetchCol();
  }

  /**
   * {@inheritdoc}
   */
  public function countDefaultLanguageRevisions(MaintenanceRecordInterface $entity) {
    return $this->database->query('SELECT COUNT(*) FROM {maintenance_record_field_revision} WHERE id = :id AND default_langcode = 1', [':id' => $entity->id()])
      ->fetchField();
  }

  /**
   * {@inheritdoc}
   */
  public function clearRevisionsLanguage(LanguageInterface $language) {
    return $this->database->update('maintenance_record_revision')
      ->fields(['langcode' => LanguageInterface::LANGCODE_NOT_SPECIFIED])
      ->condition('langcode', $language->getId())
      ->execute();
  }

}
