<?php

namespace Drupal\asset_management\Controller;

use Drupal\Component\Utility\Xss;
use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\DependencyInjection\ContainerInjectionInterface;
use Drupal\Core\Url;
use Drupal\asset_management\Entity\AssetInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Class AssetController.
 *
 *  Returns responses for Asset routes.
 */
class AssetController extends ControllerBase implements ContainerInjectionInterface {

  /**
   * The date formatter.
   *
   * @var \Drupal\Core\Datetime\DateFormatter
   */
  protected $dateFormatter;

  /**
   * The renderer.
   *
   * @var \Drupal\Core\Render\Renderer
   */
  protected $renderer;

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    $instance = parent::create($container);
    $instance->dateFormatter = $container->get('date.formatter');
    $instance->renderer = $container->get('renderer');
    return $instance;
  }

  /**
   * Displays a Asset revision.
   *
   * @param int $asset_revision
   *   The Asset revision ID.
   *
   * @return array
   *   An array suitable for drupal_render().
   */
  public function revisionShow($asset_revision) {
    $asset = $this->entityTypeManager()->getStorage('asset')
      ->loadRevision($asset_revision);
    $view_builder = $this->entityTypeManager()->getViewBuilder('asset');

    return $view_builder->view($asset);
  }

  /**
   * Page title callback for a Asset revision.
   *
   * @param int $asset_revision
   *   The Asset revision ID.
   *
   * @return string
   *   The page title.
   */
  public function revisionPageTitle($asset_revision) {
    $asset = $this->entityTypeManager()->getStorage('asset')
      ->loadRevision($asset_revision);
    return $this->t('Revision of %title from %date', [
      '%title' => $asset->label(),
      '%date' => $this->dateFormatter->format($asset->getRevisionCreationTime()),
    ]);
  }

  /**
   * Generates an overview table of older revisions of a Asset.
   *
   * @param \Drupal\asset_management\Entity\AssetInterface $asset
   *   A Asset object.
   *
   * @return array
   *   An array as expected by drupal_render().
   */
  public function revisionOverview(AssetInterface $asset) {
    $account = $this->currentUser();
    $asset_storage = $this->entityTypeManager()->getStorage('asset');

    $langcode = $asset->language()->getId();
    $langname = $asset->language()->getName();
    $languages = $asset->getTranslationLanguages();
    $has_translations = (count($languages) > 1);
    $build['#title'] = $has_translations ? $this->t('@langname revisions for %title', ['@langname' => $langname, '%title' => $asset->label()]) : $this->t('Revisions for %title', ['%title' => $asset->label()]);

    $header = [$this->t('Revision'), $this->t('Operations')];
    $revert_permission = (($account->hasPermission("revert all asset revisions") || $account->hasPermission('administer asset entities')));
    $delete_permission = (($account->hasPermission("delete all asset revisions") || $account->hasPermission('administer asset entities')));

    $rows = [];

    $vids = $asset_storage->revisionIds($asset);

    $latest_revision = TRUE;

    foreach (array_reverse($vids) as $vid) {
      /** @var \Drupal\asset_management\AssetInterface $revision */
      $revision = $asset_storage->loadRevision($vid);
      // Only show revisions that are affected by the language that is being
      // displayed.
      if ($revision->hasTranslation($langcode) && $revision->getTranslation($langcode)->isRevisionTranslationAffected()) {
        $username = [
          '#theme' => 'username',
          '#account' => $revision->getRevisionUser(),
        ];

        // Use revision link to link to revisions that are not active.
        $date = $this->dateFormatter->format($revision->getRevisionCreationTime(), 'short');
        if ($vid != $asset->getRevisionId()) {
          $link = $this->l($date, new Url('entity.asset.revision', [
            'asset' => $asset->id(),
            'asset_revision' => $vid,
          ]));
        }
        else {
          $link = $asset->link($date);
        }

        $row = [];
        $column = [
          'data' => [
            '#type' => 'inline_template',
            '#template' => '{% trans %}{{ date }} by {{ username }}{% endtrans %}{% if message %}<p class="revision-log">{{ message }}</p>{% endif %}',
            '#context' => [
              'date' => $link,
              'username' => $this->renderer->renderPlain($username),
              'message' => [
                '#markup' => $revision->getRevisionLogMessage(),
                '#allowed_tags' => Xss::getHtmlTagList(),
              ],
            ],
          ],
        ];
        $row[] = $column;

        if ($latest_revision) {
          $row[] = [
            'data' => [
              '#prefix' => '<em>',
              '#markup' => $this->t('Current revision'),
              '#suffix' => '</em>',
            ],
          ];
          foreach ($row as &$current) {
            $current['class'] = ['revision-current'];
          }
          $latest_revision = FALSE;
        }
        else {
          $links = [];
          if ($revert_permission) {
            $links['revert'] = [
              'title' => $this->t('Revert'),
              'url' => $has_translations ?
              Url::fromRoute('entity.asset.translation_revert', [
                'asset' => $asset->id(),
                'asset_revision' => $vid,
                'langcode' => $langcode,
              ]) :
              Url::fromRoute('entity.asset.revision_revert', [
                'asset' => $asset->id(),
                'asset_revision' => $vid,
              ]),
            ];
          }

          if ($delete_permission) {
            $links['delete'] = [
              'title' => $this->t('Delete'),
              'url' => Url::fromRoute('entity.asset.revision_delete', [
                'asset' => $asset->id(),
                'asset_revision' => $vid,
              ]),
            ];
          }

          $row[] = [
            'data' => [
              '#type' => 'operations',
              '#links' => $links,
            ],
          ];
        }

        $rows[] = $row;
      }
    }

    $build['asset_revisions_table'] = [
      '#theme' => 'table',
      '#rows' => $rows,
      '#header' => $header,
    ];

    return $build;
  }

}
