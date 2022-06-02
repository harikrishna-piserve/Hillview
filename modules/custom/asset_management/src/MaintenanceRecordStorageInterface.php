<?php

namespace Drupal\asset_management;

use Drupal\Core\Entity\ContentEntityStorageInterface;
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
interface MaintenanceRecordStorageInterface extends ContentEntityStorageInterface {

  /**
   * Gets a list of Maintenance record revision IDs for a specific Maintenance record.
   *
   * @param \Drupal\asset_management\Entity\MaintenanceRecordInterface $entity
   *   The Maintenance record entity.
   *
   * @return int[]
   *   Maintenance record revision IDs (in ascending order).
   */
  public function revisionIds(MaintenanceRecordInterface $entity);

  /**
   * Gets a list of revision IDs having a given user as Maintenance record author.
   *
   * @param \Drupal\Core\Session\AccountInterface $account
   *   The user entity.
   *
   * @return int[]
   *   Maintenance record revision IDs (in ascending order).
   */
  public function userRevisionIds(AccountInterface $account);

  /**
   * Counts the number of revisions in the default language.
   *
   * @param \Drupal\asset_management\Entity\MaintenanceRecordInterface $entity
   *   The Maintenance record entity.
   *
   * @return int
   *   The number of revisions in the default language.
   */
  public function countDefaultLanguageRevisions(MaintenanceRecordInterface $entity);

  /**
   * Unsets the language for all Maintenance record with the given language.
   *
   * @param \Drupal\Core\Language\LanguageInterface $language
   *   The language object.
   */
  public function clearRevisionsLanguage(LanguageInterface $language);

}
