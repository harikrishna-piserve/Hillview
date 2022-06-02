<?php

namespace Drupal\asset_management\Controller;

use Drupal\Component\Utility\Xss;
use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\DependencyInjection\ContainerInjectionInterface;
use Drupal\Core\Url;
use Drupal\asset_management\Entity\AssignationInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Class AssignationController.
 *
 *  Returns responses for Assignation routes.
 */
class AssignationController extends ControllerBase implements ContainerInjectionInterface {

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
   * Displays a Assignation revision.
   *
   * @param int $assignation_revision
   *   The Assignation revision ID.
   *
   * @return array
   *   An array suitable for drupal_render().
   */
  public function revisionShow($assignation_revision) {
    $assignation = $this->entityTypeManager()->getStorage('assignation')
      ->loadRevision($assignation_revision);
    $view_builder = $this->entityTypeManager()->getViewBuilder('assignation');

    return $view_builder->view($assignation);
  }

  /**
   * Page title callback for a Assignation revision.
   *
   * @param int $assignation_revision
   *   The Assignation revision ID.
   *
   * @return string
   *   The page title.
   */
  public function revisionPageTitle($assignation_revision) {
    $assignation = $this->entityTypeManager()->getStorage('assignation')
      ->loadRevision($assignation_revision);
    return $this->t('Revision of %title from %date', [
      '%title' => $assignation->label(),
      '%date' => $this->dateFormatter->format($assignation->getRevisionCreationTime()),
    ]);
  }

  /**
   * Generates an overview table of older revisions of a Assignation.
   *
   * @param \Drupal\asset_management\Entity\AssignationInterface $assignation
   *   A Assignation object.
   *
   * @return array
   *   An array as expected by drupal_render().
   */
  public function revisionOverview(AssignationInterface $assignation) {
    $account = $this->currentUser();
    $assignation_storage = $this->entityTypeManager()->getStorage('assignation');

    $langcode = $assignation->language()->getId();
    $langname = $assignation->language()->getName();
    $languages = $assignation->getTranslationLanguages();
    $has_translations = (count($languages) > 1);
    $build['#title'] = $has_translations ? $this->t('@langname revisions for %title', ['@langname' => $langname, '%title' => $assignation->label()]) : $this->t('Revisions for %title', ['%title' => $assignation->label()]);

    $header = [$this->t('Revision'), $this->t('Operations')];
    $revert_permission = (($account->hasPermission("revert all assignation revisions") || $account->hasPermission('administer assignation entities')));
    $delete_permission = (($account->hasPermission("delete all assignation revisions") || $account->hasPermission('administer assignation entities')));

    $rows = [];

    $vids = $assignation_storage->revisionIds($assignation);

    $latest_revision = TRUE;

    foreach (array_reverse($vids) as $vid) {
      /** @var \Drupal\asset_management\AssignationInterface $revision */
      $revision = $assignation_storage->loadRevision($vid);
      // Only show revisions that are affected by the language that is being
      // displayed.
      if ($revision->hasTranslation($langcode) && $revision->getTranslation($langcode)->isRevisionTranslationAffected()) {
        $username = [
          '#theme' => 'username',
          '#account' => $revision->getRevisionUser(),
        ];

        // Use revision link to link to revisions that are not active.
        $date = $this->dateFormatter->format($revision->getRevisionCreationTime(), 'short');
        if ($vid != $assignation->getRevisionId()) {
          $link = $this->l($date, new Url('entity.assignation.revision', [
            'assignation' => $assignation->id(),
            'assignation_revision' => $vid,
          ]));
        }
        else {
          $link = $assignation->link($date);
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
              Url::fromRoute('entity.assignation.translation_revert', [
                'assignation' => $assignation->id(),
                'assignation_revision' => $vid,
                'langcode' => $langcode,
              ]) :
              Url::fromRoute('entity.assignation.revision_revert', [
                'assignation' => $assignation->id(),
                'assignation_revision' => $vid,
              ]),
            ];
          }

          if ($delete_permission) {
            $links['delete'] = [
              'title' => $this->t('Delete'),
              'url' => Url::fromRoute('entity.assignation.revision_delete', [
                'assignation' => $assignation->id(),
                'assignation_revision' => $vid,
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

    $build['assignation_revisions_table'] = [
      '#theme' => 'table',
      '#rows' => $rows,
      '#header' => $header,
    ];

    return $build;
  }

}
