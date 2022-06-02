<?php

namespace Drupal\asset_management;

use Drupal\Core\Entity\Sql\SqlContentEntityStorage;
use Drupal\Core\Session\AccountInterface;
use Drupal\Core\Language\LanguageInterface;
use Drupal\asset_management\Entity\AssignationInterface;

/**
 * Defines the storage handler class for Assignation entities.
 *
 * This extends the base storage class, adding required special handling for
 * Assignation entities.
 *
 * @ingroup asset_management
 */
class AssignationStorage extends SqlContentEntityStorage implements AssignationStorageInterface {

  /**
   * {@inheritdoc}
   */
  public function revisionIds(AssignationInterface $entity) {
    return $this->database->query(
      'SELECT vid FROM {assignation_revision} WHERE id=:id ORDER BY vid',
      [':id' => $entity->id()]
    )->fetchCol();
  }

  /**
   * {@inheritdoc}
   */
  public function userRevisionIds(AccountInterface $account) {
    return $this->database->query(
      'SELECT vid FROM {assignation_field_revision} WHERE uid = :uid ORDER BY vid',
      [':uid' => $account->id()]
    )->fetchCol();
  }

  /**
   * {@inheritdoc}
   */
  public function countDefaultLanguageRevisions(AssignationInterface $entity) {
    return $this->database->query('SELECT COUNT(*) FROM {assignation_field_revision} WHERE id = :id AND default_langcode = 1', [':id' => $entity->id()])
      ->fetchField();
  }

  /**
   * {@inheritdoc}
   */
  public function clearRevisionsLanguage(LanguageInterface $language) {
    return $this->database->update('assignation_revision')
      ->fields(['langcode' => LanguageInterface::LANGCODE_NOT_SPECIFIED])
      ->condition('langcode', $language->getId())
      ->execute();
  }

}
