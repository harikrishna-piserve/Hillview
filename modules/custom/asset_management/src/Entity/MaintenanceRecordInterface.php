<?php

namespace Drupal\asset_management\Entity;

use Drupal\Core\Entity\ContentEntityInterface;
use Drupal\Core\Entity\RevisionLogInterface;
use Drupal\Core\Entity\EntityChangedInterface;
use Drupal\Core\Entity\EntityPublishedInterface;
use Drupal\user\EntityOwnerInterface;

/**
 * Provides an interface for defining Maintenance record entities.
 *
 * @ingroup asset_management
 */
interface MaintenanceRecordInterface extends ContentEntityInterface, RevisionLogInterface, EntityChangedInterface, EntityPublishedInterface, EntityOwnerInterface {

  /**
   * Add get/set methods for your configuration properties here.
   */

  /**
   * Gets the Maintenance record name.
   *
   * @return string
   *   Name of the Maintenance record.
   */
  public function getName();

  /**
   * Sets the Maintenance record name.
   *
   * @param string $name
   *   The Maintenance record name.
   *
   * @return \Drupal\asset_management\Entity\MaintenanceRecordInterface
   *   The called Maintenance record entity.
   */
  public function setName($name);

  /**
   * Gets the Maintenance record creation timestamp.
   *
   * @return int
   *   Creation timestamp of the Maintenance record.
   */
  public function getCreatedTime();

  /**
   * Sets the Maintenance record creation timestamp.
   *
   * @param int $timestamp
   *   The Maintenance record creation timestamp.
   *
   * @return \Drupal\asset_management\Entity\MaintenanceRecordInterface
   *   The called Maintenance record entity.
   */
  public function setCreatedTime($timestamp);

  /**
   * Gets the Maintenance record revision creation timestamp.
   *
   * @return int
   *   The UNIX timestamp of when this revision was created.
   */
  public function getRevisionCreationTime();

  /**
   * Sets the Maintenance record revision creation timestamp.
   *
   * @param int $timestamp
   *   The UNIX timestamp of when this revision was created.
   *
   * @return \Drupal\asset_management\Entity\MaintenanceRecordInterface
   *   The called Maintenance record entity.
   */
  public function setRevisionCreationTime($timestamp);

  /**
   * Gets the Maintenance record revision author.
   *
   * @return \Drupal\user\UserInterface
   *   The user entity for the revision author.
   */
  public function getRevisionUser();

  /**
   * Sets the Maintenance record revision author.
   *
   * @param int $uid
   *   The user ID of the revision author.
   *
   * @return \Drupal\asset_management\Entity\MaintenanceRecordInterface
   *   The called Maintenance record entity.
   */
  public function setRevisionUserId($uid);

}
