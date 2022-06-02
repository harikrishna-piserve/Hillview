<?php

namespace Drupal\asset_management\Form;

use Drupal\Core\Form\ConfirmFormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Url;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Provides a form for deleting a Maintenance record revision.
 *
 * @ingroup asset_management
 */
class MaintenanceRecordRevisionDeleteForm extends ConfirmFormBase {

  /**
   * The Maintenance record revision.
   *
   * @var \Drupal\asset_management\Entity\MaintenanceRecordInterface
   */
  protected $revision;

  /**
   * The Maintenance record storage.
   *
   * @var \Drupal\Core\Entity\EntityStorageInterface
   */
  protected $maintenanceRecordStorage;

  /**
   * The database connection.
   *
   * @var \Drupal\Core\Database\Connection
   */
  protected $connection;

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    $instance = parent::create($container);
    $instance->maintenanceRecordStorage = $container->get('entity_type.manager')->getStorage('maintenance_record');
    $instance->connection = $container->get('database');
    return $instance;
  }

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'maintenance_record_revision_delete_confirm';
  }

  /**
   * {@inheritdoc}
   */
  public function getQuestion() {
    return $this->t('Are you sure you want to delete the revision from %revision-date?', [
      '%revision-date' => format_date($this->revision->getRevisionCreationTime()),
    ]);
  }

  /**
   * {@inheritdoc}
   */
  public function getCancelUrl() {
    return new Url('entity.maintenance_record.version_history', ['maintenance_record' => $this->revision->id()]);
  }

  /**
   * {@inheritdoc}
   */
  public function getConfirmText() {
    return $this->t('Delete');
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state, $maintenance_record_revision = NULL) {
    $this->revision = $this->MaintenanceRecordStorage->loadRevision($maintenance_record_revision);
    $form = parent::buildForm($form, $form_state);

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    $this->MaintenanceRecordStorage->deleteRevision($this->revision->getRevisionId());

    $this->logger('content')->notice('Maintenance record: deleted %title revision %revision.', ['%title' => $this->revision->label(), '%revision' => $this->revision->getRevisionId()]);
    $this->messenger()->addMessage(t('Revision from %revision-date of Maintenance record %title has been deleted.', ['%revision-date' => format_date($this->revision->getRevisionCreationTime()), '%title' => $this->revision->label()]));
    $form_state->setRedirect(
      'entity.maintenance_record.canonical',
       ['maintenance_record' => $this->revision->id()]
    );
    if ($this->connection->query('SELECT COUNT(DISTINCT vid) FROM {maintenance_record_field_revision} WHERE id = :id', [':id' => $this->revision->id()])->fetchField() > 1) {
      $form_state->setRedirect(
        'entity.maintenance_record.version_history',
         ['maintenance_record' => $this->revision->id()]
      );
    }
  }

}
