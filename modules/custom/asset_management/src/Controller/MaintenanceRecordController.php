<?php

namespace Drupal\asset_management\Controller;

use Drupal\Component\Utility\Xss;
use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\DependencyInjection\ContainerInjectionInterface;
use Drupal\Core\Url;
use Drupal\asset_management\Entity\MaintenanceRecordInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Class MaintenanceRecordController.
 *
 *  Returns responses for Maintenance record routes.
 */
class MaintenanceRecordController extends ControllerBase implements ContainerInjectionInterface {

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
   * Displays a Maintenance record revision.
   *
   * @param int $maintenance_record_revision
   *   The Maintenance record revision ID.
   *
   * @return array
   *   An array suitable for drupal_render().
   */
  public function revisionShow($maintenance_record_revision) {
    $maintenance_record = $this->entityTypeManager()->getStorage('maintenance_record')
      ->loadRevision($maintenance_record_revision);
    $view_builder = $this->entityTypeManager()->getViewBuilder('maintenance_record');

    return $view_builder->view($maintenance_record);
  }

  /**
   * Page title callback for a Maintenance record revision.
   *
   * @param int $maintenance_record_revision
   *   The Maintenance record revision ID.
   *
   * @return string
   *   The page title.
   */
  public function revisionPageTitle($maintenance_record_revision) {
    $maintenance_record = $this->entityTypeManager()->getStorage('maintenance_record')
      ->loadRevision($maintenance_record_revision);
    return $this->t('Revision of %title from %date', [
      '%title' => $maintenance_record->label(),
      '%date' => $this->dateFormatter->format($maintenance_record->getRevisionCreationTime()),
    ]);
  }

  /**
   * Generates an overview table of older revisions of a Maintenance record.
   *
   * @param \Drupal\asset_management\Entity\MaintenanceRecordInterface $maintenance_record
   *   A Maintenance record object.
   *
   * @return array
   *   An array as expected by drupal_render().
   */
  public function revisionOverview(MaintenanceRecordInterface $maintenance_record) {
    $account = $this->currentUser();
    $maintenance_record_storage = $this->entityTypeManager()->getStorage('maintenance_record');

    $langcode = $maintenance_record->language()->getId();
    $langname = $maintenance_record->language()->getName();
    $languages = $maintenance_record->getTranslationLanguages();
    $has_translations = (count($languages) > 1);
    $build['#title'] = $has_translations ? $this->t('@langname revisions for %title', ['@langname' => $langname, '%title' => $maintenance_record->label()]) : $this->t('Revisions for %title', ['%title' => $maintenance_record->label()]);

    $header = [$this->t('Revision'), $this->t('Operations')];
    $revert_permission = (($account->hasPermission("revert all maintenance record revisions") || $account->hasPermission('administer maintenance record entities')));
    $delete_permission = (($account->hasPermission("delete all maintenance record revisions") || $account->hasPermission('administer maintenance record entities')));

    $rows = [];

    $vids = $maintenance_record_storage->revisionIds($maintenance_record);

    $latest_revision = TRUE;

    foreach (array_reverse($vids) as $vid) {
      /** @var \Drupal\asset_management\MaintenanceRecordInterface $revision */
      $revision = $maintenance_record_storage->loadRevision($vid);
      // Only show revisions that are affected by the language that is being
      // displayed.
      if ($revision->hasTranslation($langcode) && $revision->getTranslation($langcode)->isRevisionTranslationAffected()) {
        $username = [
          '#theme' => 'username',
          '#account' => $revision->getRevisionUser(),
        ];

        // Use revision link to link to revisions that are not active.
        $date = $this->dateFormatter->format($revision->getRevisionCreationTime(), 'short');
        if ($vid != $maintenance_record->getRevisionId()) {
          $link = $this->l($date, new Url('entity.maintenance_record.revision', [
            'maintenance_record' => $maintenance_record->id(),
            'maintenance_record_revision' => $vid,
          ]));
        }
        else {
          $link = $maintenance_record->link($date);
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
              Url::fromRoute('entity.maintenance_record.translation_revert', [
                'maintenance_record' => $maintenance_record->id(),
                'maintenance_record_revision' => $vid,
                'langcode' => $langcode,
              ]) :
              Url::fromRoute('entity.maintenance_record.revision_revert', [
                'maintenance_record' => $maintenance_record->id(),
                'maintenance_record_revision' => $vid,
              ]),
            ];
          }

          if ($delete_permission) {
            $links['delete'] = [
              'title' => $this->t('Delete'),
              'url' => Url::fromRoute('entity.maintenance_record.revision_delete', [
                'maintenance_record' => $maintenance_record->id(),
                'maintenance_record_revision' => $vid,
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

    $build['maintenance_record_revisions_table'] = [
      '#theme' => 'table',
      '#rows' => $rows,
      '#header' => $header,
    ];

    return $build;
  }

}
