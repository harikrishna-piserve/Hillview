<?php

namespace Drupal\asset_management\Entity;

use Drupal\Core\Config\Entity\ConfigEntityBundleBase;

/**
 * Defines the Asset type entity.
 *
 * @ConfigEntityType(
 *   id = "asset_type",
 *   label = @Translation("Asset type"),
 *   handlers = {
 *     "view_builder" = "Drupal\Core\Entity\EntityViewBuilder",
 *     "list_builder" = "Drupal\asset_management\AssetTypeListBuilder",
 *     "form" = {
 *       "add" = "Drupal\asset_management\Form\AssetTypeForm",
 *       "edit" = "Drupal\asset_management\Form\AssetTypeForm",
 *       "delete" = "Drupal\asset_management\Form\AssetTypeDeleteForm"
 *     },
 *     "route_provider" = {
 *       "html" = "Drupal\asset_management\AssetTypeHtmlRouteProvider",
 *     },
 *   },
 *   config_prefix = "asset_type",
 *   admin_permission = "administer site configuration",
 *   bundle_of = "asset",
 *   entity_keys = {
 *     "id" = "id",
 *     "label" = "label",
 *     "uuid" = "uuid"
 *   },
 *   links = {
 *     "canonical" = "/admin/structure/asset_type/{asset_type}",
 *     "add-form" = "/admin/structure/asset_type/add",
 *     "edit-form" = "/admin/structure/asset_type/{asset_type}/edit",
 *     "delete-form" = "/admin/structure/asset_type/{asset_type}/delete",
 *     "collection" = "/admin/structure/asset_type"
 *   },
 * 
 *   config_export = {
 *     "id",
 *     "label",
 *     "prefixid",
 *     "prefix"
 *   }
 * )
 */
class AssetType extends ConfigEntityBundleBase implements AssetTypeInterface {

  /**
   * The Asset type ID.
   *
   * @var string
   */
  protected $id;

  /**
   * The Asset type label.
   *
   * @var string
   */
  protected $label;

  /**
   * The Asset type prefix id.
   *
   * @var string
   */
  protected $prefixid;

   /**
   * The Asset type prefix id increment.
   *
   * @var string
   */
  protected $prefix;

  public function getPrefixid()
  {
    return $this->prefixid;
  }

  public function getPrefix()
  {
    return $this->prefix;
  }


  

}
