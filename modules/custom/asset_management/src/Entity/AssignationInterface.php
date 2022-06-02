<?php

namespace Drupal\asset_management\Entity;

use Drupal\Core\Entity\ContentEntityInterface;
use Drupal\Core\Entity\RevisionLogInterface;
use Drupal\Core\Entity\EntityChangedInterface;
use Drupal\Core\Entity\EntityPublishedInterface;
use Drupal\user\EntityOwnerInterface;

/**
 * Provides an interface for defining Assignation entities.
 *
 * @ingroup asset_management
 */
interface AssignationInterface extends ContentEntityInterface, RevisionLogInterface, EntityChangedInterface, EntityPublishedInterface, EntityOwnerInterface {

  // /**
  //  * Add get/set methods for your configuration properties here.
  //  */

  // /**
  //  * Gets the Assignation name.
  //  *
  //  * @return string
  //  *   Name of the Assignation.
  //  */
  // public function getName();

  // /**
  //  * Sets the Assignation name.
  //  *
  //  * @param string $name
  //  *   The Assignation name.
  //  *
  //  * @return \Drupal\asset_management\Entity\AssignationInterface
  //  *   The called Assignation entity.
  //  */
  // public function setName($name);

  /**
   * Gets the Assignation creation timestamp.
   *
   * @return int
   *   Creation timestamp of the Assignation.
   */
  public function getCreatedTime();

  /**
   * Sets the Assignation creation timestamp.
   *
   * @param int $timestamp
   *   The Assignation creation timestamp.
   *
   * @return \Drupal\asset_management\Entity\AssignationInterface
   *   The called Assignation entity.
   */
  public function setCreatedTime($timestamp);

  /**
   * Gets the Assignation revision creation timestamp.
   *
   * @return int
   *   The UNIX timestamp of when this revision was created.
   */
  public function getRevisionCreationTime();

  /**
   * Sets the Assignation revision creation timestamp.
   *
   * @param int $timestamp
   *   The UNIX timestamp of when this revision was created.
   *
   * @return \Drupal\asset_management\Entity\AssignationInterface
   *   The called Assignation entity.
   */
  public function setRevisionCreationTime($timestamp);

  /**
   * Gets the Assignation revision author.
   *
   * @return \Drupal\user\UserInterface
   *   The user entity for the revision author.
   */
  public function getRevisionUser();

  /**
   * Sets the Assignation revision author.
   *
   * @param int $uid
   *   The user ID of the revision author.
   *
   * @return \Drupal\asset_management\Entity\AssignationInterface
   *   The called Assignation entity.
   */
  public function setRevisionUserId($uid);

}
