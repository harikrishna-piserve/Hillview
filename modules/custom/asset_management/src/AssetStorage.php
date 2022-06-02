<?php

namespace Drupal\asset_management;

use Drupal\Core\Entity\Sql\SqlContentEntityStorage;
use Drupal\Core\Session\AccountInterface;
use Drupal\Core\Language\LanguageInterface;
use Drupal\asset_management\Entity\AssetInterface;

/**
 * Defines the storage handler class for Asset entities.
 *
 * This extends the base storage class, adding required special handling for
 * Asset entities.
 *
 * @ingroup asset_management
 */
class AssetStorage extends SqlContentEntityStorage implements AssetStorageInterface {

  /**
   * {@inheritdoc}
   */
  public function revisionIds(AssetInterface $entity) {
    return $this->database->query(
      'SELECT vid FROM {asset_revision} WHERE id=:id ORDER BY vid',
      [':id' => $entity->id()]
    )->fetchCol();
  }

  /**
   * {@inheritdoc}
   */
  public function userRevisionIds(AccountInterface $account) {
    return $this->database->query(
      'SELECT vid FROM {asset_field_revision} WHERE uid = :uid ORDER BY vid',
      [':uid' => $account->id()]
    )->fetchCol();
  }

  /**
   * {@inheritdoc}
   */
  public function countDefaultLanguageRevisions(AssetInterface $entity) {
    return $this->database->query('SELECT COUNT(*) FROM {asset_field_revision} WHERE id = :id AND default_langcode = 1', [':id' => $entity->id()])
      ->fetchField();
  }

  /**
   * {@inheritdoc}
   */
  public function clearRevisionsLanguage(LanguageInterface $language) {
    return $this->database->update('asset_revision')
      ->fields(['langcode' => LanguageInterface::LANGCODE_NOT_SPECIFIED])
      ->condition('langcode', $language->getId())
      ->execute();
  }

}
