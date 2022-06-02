<?php

namespace Drupal\asset_management\Controller;

use Drupal\Component\Utility\Xss;
use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\DependencyInjection\ContainerInjectionInterface;
use Drupal\Core\Url;
use Drupal\asset_management\Entity\VendorRecordInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Class VendorRecordController.
 *
 *  Returns responses for Vendor record routes.
 */
class VendorRecordController extends ControllerBase implements ContainerInjectionInterface {

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
   * Displays a Vendor record revision.
   *
   * @param int $vendor_record_revision
   *   The Vendor record revision ID.
   *
   * @return array
   *   An array suitable for drupal_render().
   */
  public function revisionShow($vendor_record_revision) {
    $vendor_record = $this->entityTypeManager()->getStorage('vendor_record')
      ->loadRevision($vendor_record_revision);
    $view_builder = $this->entityTypeManager()->getViewBuilder('vendor_record');

    return $view_builder->view($vendor_record);
  }

  /**
   * Page title callback for a Vendor record revision.
   *
   * @param int $vendor_record_revision
   *   The Vendor record revision ID.
   *
   * @return string
   *   The page title.
   */
  public function revisionPageTitle($vendor_record_revision) {
    $vendor_record = $this->entityTypeManager()->getStorage('vendor_record')
      ->loadRevision($vendor_record_revision);
    return $this->t('Revision of %title from %date', [
      '%title' => $vendor_record->label(),
      '%date' => $this->dateFormatter->format($vendor_record->getRevisionCreationTime()),
    ]);
  }

  /**
   * Generates an overview table of older revisions of a Vendor record.
   *
   * @param \Drupal\asset_management\Entity\VendorRecordInterface $vendor_record
   *   A Vendor record object.
   *
   * @return array
   *   An array as expected by drupal_render().
   */
  public function revisionOverview(VendorRecordInterface $vendor_record) {
    $account = $this->currentUser();
    $vendor_record_storage = $this->entityTypeManager()->getStorage('vendor_record');

    $langcode = $vendor_record->language()->getId();
    $langname = $vendor_record->language()->getName();
    $languages = $vendor_record->getTranslationLanguages();
    $has_translations = (count($languages) > 1);
    $build['#title'] = $has_translations ? $this->t('@langname revisions for %title', ['@langname' => $langname, '%title' => $vendor_record->label()]) : $this->t('Revisions for %title', ['%title' => $vendor_record->label()]);

    $header = [$this->t('Revision'), $this->t('Operations')];
    $revert_permission = (($account->hasPermission("revert all vendor record revisions") || $account->hasPermission('administer vendor record entities')));
    $delete_permission = (($account->hasPermission("delete all vendor record revisions") || $account->hasPermission('administer vendor record entities')));

    $rows = [];

    $vids = $vendor_record_storage->revisionIds($vendor_record);

    $latest_revision = TRUE;

    foreach (array_reverse($vids) as $vid) {
      /** @var \Drupal\asset_management\VendorRecordInterface $revision */
      $revision = $vendor_record_storage->loadRevision($vid);
      // Only show revisions that are affected by the language that is being
      // displayed.
      if ($revision->hasTranslation($langcode) && $revision->getTranslation($langcode)->isRevisionTranslationAffected()) {
        $username = [
          '#theme' => 'username',
          '#account' => $revision->getRevisionUser(),
        ];

        // Use revision link to link to revisions that are not active.
        $date = $this->dateFormatter->format($revision->getRevisionCreationTime(), 'short');
        if ($vid != $vendor_record->getRevisionId()) {
          $link = $this->l($date, new Url('entity.vendor_record.revision', [
            'vendor_record' => $vendor_record->id(),
            'vendor_record_revision' => $vid,
          ]));
        }
        else {
          $link = $vendor_record->link($date);
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
              Url::fromRoute('entity.vendor_record.translation_revert', [
                'vendor_record' => $vendor_record->id(),
                'vendor_record_revision' => $vid,
                'langcode' => $langcode,
              ]) :
              Url::fromRoute('entity.vendor_record.revision_revert', [
                'vendor_record' => $vendor_record->id(),
                'vendor_record_revision' => $vid,
              ]),
            ];
          }

          if ($delete_permission) {
            $links['delete'] = [
              'title' => $this->t('Delete'),
              'url' => Url::fromRoute('entity.vendor_record.revision_delete', [
                'vendor_record' => $vendor_record->id(),
                'vendor_record_revision' => $vid,
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

    $build['vendor_record_revisions_table'] = [
      '#theme' => 'table',
      '#rows' => $rows,
      '#header' => $header,
    ];

    return $build;
  }

}
