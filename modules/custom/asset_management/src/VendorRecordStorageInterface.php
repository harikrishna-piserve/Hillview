<?php

namespace Drupal\asset_management;

use Drupal\Core\Entity\ContentEntityStorageInterface;
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
interface VendorRecordStorageInterface extends ContentEntityStorageInterface {

  /**
   * Gets a list of Vendor record revision IDs for a specific Vendor record.
   *
   * @param \Drupal\asset_management\Entity\VendorRecordInterface $entity
   *   The Vendor record entity.
   *
   * @return int[]
   *   Vendor record revision IDs (in ascending order).
   */
  public function revisionIds(VendorRecordInterface $entity);

  /**
   * Gets a list of revision IDs having a given user as Vendor record author.
   *
   * @param \Drupal\Core\Session\AccountInterface $account
   *   The user entity.
   *
   * @return int[]
   *   Vendor record revision IDs (in ascending order).
   */
  public function userRevisionIds(AccountInterface $account);

  /**
   * Counts the number of revisions in the default language.
   *
   * @param \Drupal\asset_management\Entity\VendorRecordInterface $entity
   *   The Vendor record entity.
   *
   * @return int
   *   The number of revisions in the default language.
   */
  public function countDefaultLanguageRevisions(VendorRecordInterface $entity);

  /**
   * Unsets the language for all Vendor record with the given language.
   *
   * @param \Drupal\Core\Language\LanguageInterface $language
   *   The language object.
   */
  public function clearRevisionsLanguage(LanguageInterface $language);

}
