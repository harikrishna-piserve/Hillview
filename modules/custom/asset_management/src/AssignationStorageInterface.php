<?php

namespace Drupal\asset_management;

use Drupal\Core\Entity\ContentEntityStorageInterface;
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
interface AssignationStorageInterface extends ContentEntityStorageInterface {

  /**
   * Gets a list of Assignation revision IDs for a specific Assignation.
   *
   * @param \Drupal\asset_management\Entity\AssignationInterface $entity
   *   The Assignation entity.
   *
   * @return int[]
   *   Assignation revision IDs (in ascending order).
   */
  public function revisionIds(AssignationInterface $entity);

  /**
   * Gets a list of revision IDs having a given user as Assignation author.
   *
   * @param \Drupal\Core\Session\AccountInterface $account
   *   The user entity.
   *
   * @return int[]
   *   Assignation revision IDs (in ascending order).
   */
  public function userRevisionIds(AccountInterface $account);

  /**
   * Counts the number of revisions in the default language.
   *
   * @param \Drupal\asset_management\Entity\AssignationInterface $entity
   *   The Assignation entity.
   *
   * @return int
   *   The number of revisions in the default language.
   */
  public function countDefaultLanguageRevisions(AssignationInterface $entity);

  /**
   * Unsets the language for all Assignation with the given language.
   *
   * @param \Drupal\Core\Language\LanguageInterface $language
   *   The language object.
   */
  public function clearRevisionsLanguage(LanguageInterface $language);

}
