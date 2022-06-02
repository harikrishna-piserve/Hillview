<?php

namespace Drupal\asset_management\Entity;

use Drupal\Core\Entity\ContentEntityInterface;
use Drupal\Core\Entity\RevisionLogInterface;
use Drupal\Core\Entity\EntityChangedInterface;
use Drupal\Core\Entity\EntityPublishedInterface;
use Drupal\user\EntityOwnerInterface;

/**
 * Provides an interface for defining Asset entities.
 *
 * @ingroup asset_management
 */
interface AssetInterface extends ContentEntityInterface, RevisionLogInterface, EntityChangedInterface, EntityPublishedInterface, EntityOwnerInterface {

  /**
   * Add get/set methods for your configuration properties here.
   */

  /**
   * Gets the Asset name.
   *
   * @return string
   *   Name of the Asset.
   */
  public function getName();

  /**
   * Sets the Asset name.
   *
   * @param string $name
   *   The Asset name.
   *
   * @return \Drupal\asset_management\Entity\AssetInterface
   *   The called Asset entity.
   */
  public function setName($name);

  /**
   * Gets the Asset creation timestamp.
   *
   * @return int
   *   Creation timestamp of the Asset.
   */
  public function getCreatedTime();

  /**
   * Sets the Asset creation timestamp.
   *
   * @param int $timestamp
   *   The Asset creation timestamp.
   *
   * @return \Drupal\asset_management\Entity\AssetInterface
   *   The called Asset entity.
   */
  public function setCreatedTime($timestamp);

  /**
   * Gets the Asset revision creation timestamp.
   *
   * @return int
   *   The UNIX timestamp of when this revision was created.
   */
  public function getRevisionCreationTime();

  /**
   * Sets the Asset revision creation timestamp.
   *
   * @param int $timestamp
   *   The UNIX timestamp of when this revision was created.
   *
   * @return \Drupal\asset_management\Entity\AssetInterface
   *   The called Asset entity.
   */
  public function setRevisionCreationTime($timestamp);

  /**
   * Gets the Asset revision author.
   *
   * @return \Drupal\user\UserInterface
   *   The user entity for the revision author.
   */
  public function getRevisionUser();

  /**
   * Sets the Asset revision author.
   *
   * @param int $uid
   *   The user ID of the revision author.
   *
   * @return \Drupal\asset_management\Entity\AssetInterface
   *   The called Asset entity.
   */
  public function setRevisionUserId($uid);

}
