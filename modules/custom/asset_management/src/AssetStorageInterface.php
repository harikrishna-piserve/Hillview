<?php

namespace Drupal\asset_management;

use Drupal\Core\Entity\ContentEntityStorageInterface;
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
interface AssetStorageInterface extends ContentEntityStorageInterface {

  /**
   * Gets a list of Asset revision IDs for a specific Asset.
   *
   * @param \Drupal\asset_management\Entity\AssetInterface $entity
   *   The Asset entity.
   *
   * @return int[]
   *   Asset revision IDs (in ascending order).
   */
  public function revisionIds(AssetInterface $entity);

  /**
   * Gets a list of revision IDs having a given user as Asset author.
   *
   * @param \Drupal\Core\Session\AccountInterface $account
   *   The user entity.
   *
   * @return int[]
   *   Asset revision IDs (in ascending order).
   */
  public function userRevisionIds(AccountInterface $account);

  /**
   * Counts the number of revisions in the default language.
   *
   * @param \Drupal\asset_management\Entity\AssetInterface $entity
   *   The Asset entity.
   *
   * @return int
   *   The number of revisions in the default language.
   */
  public function countDefaultLanguageRevisions(AssetInterface $entity);

  /**
   * Unsets the language for all Asset with the given language.
   *
   * @param \Drupal\Core\Language\LanguageInterface $language
   *   The language object.
   */
  public function clearRevisionsLanguage(LanguageInterface $language);

}
