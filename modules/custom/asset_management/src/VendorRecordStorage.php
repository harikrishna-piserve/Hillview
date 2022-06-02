<?php

namespace Drupal\asset_management;

use Drupal\Core\Entity\Sql\SqlContentEntityStorage;
use Drupal\Core\Session\AccountInterface;
use Drupal\Core\Language\LanguageInterface;
use Drupal\asset_management\Entity\VendorRecordInterface;

/**
 * Defines the storage handler class for Vendor record entities.
 *
 * This extends the base storage class, adding required special handling for
 * Vendor record entities.
 *
 * @ingroup asset_management
 */
class VendorRecordStorage extends SqlContentEntityStorage implements VendorRecordStorageInterface {

  /**
   * {@inheritdoc}
   */
  public function revisionIds(VendorRecordInterface $entity) {
    return $this->database->query(
      'SELECT vid FROM {vendor_record_revision} WHERE id=:id ORDER BY vid',
      [':id' => $entity->id()]
    )->fetchCol();
  }

  /**
   * {@inheritdoc}
   */
  public function userRevisionIds(AccountInterface $account) {
    return $this->database->query(
      'SELECT vid FROM {vendor_record_field_revision} WHERE uid = :uid ORDER BY vid',
      [':uid' => $account->id()]
    )->fetchCol();
  }

  /**
   * {@inheritdoc}
   */
  public function countDefaultLanguageRevisions(VendorRecordInterface $entity) {
    return $this->database->query('SELECT COUNT(*) FROM {vendor_record_field_revision} WHERE id = :id AND default_langcode = 1', [':id' => $entity->id()])
      ->fetchField();
  }

  /**
   * {@inheritdoc}
   */
  public function clearRevisionsLanguage(LanguageInterface $language) {
    return $this->database->update('vendor_record_revision')
      ->fields(['langcode' => LanguageInterface::LANGCODE_NOT_SPECIFIED])
      ->condition('langcode', $language->getId())
      ->execute();
  }

}
