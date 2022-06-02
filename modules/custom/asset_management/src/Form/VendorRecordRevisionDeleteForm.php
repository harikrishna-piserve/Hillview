<?php

namespace Drupal\asset_management\Form;

use Drupal\Core\Form\ConfirmFormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Url;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Provides a form for deleting a Vendor record revision.
 *
 * @ingroup asset_management
 */
class VendorRecordRevisionDeleteForm extends ConfirmFormBase {

  /**
   * The Vendor record revision.
   *
   * @var \Drupal\asset_management\Entity\VendorRecordInterface
   */
  protected $revision;

  /**
   * The Vendor record storage.
   *
   * @var \Drupal\Core\Entity\EntityStorageInterface
   */
  protected $vendorRecordStorage;

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
    $instance->vendorRecordStorage = $container->get('entity_type.manager')->getStorage('vendor_record');
    $instance->connection = $container->get('database');
    return $instance;
  }

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'vendor_record_revision_delete_confirm';
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
    return new Url('entity.vendor_record.version_history', ['vendor_record' => $this->revision->id()]);
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
  public function buildForm(array $form, FormStateInterface $form_state, $vendor_record_revision = NULL) {
    $this->revision = $this->VendorRecordStorage->loadRevision($vendor_record_revision);
    $form = parent::buildForm($form, $form_state);

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    $this->VendorRecordStorage->deleteRevision($this->revision->getRevisionId());

    $this->logger('content')->notice('Vendor record: deleted %title revision %revision.', ['%title' => $this->revision->label(), '%revision' => $this->revision->getRevisionId()]);
    $this->messenger()->addMessage(t('Revision from %revision-date of Vendor record %title has been deleted.', ['%revision-date' => format_date($this->revision->getRevisionCreationTime()), '%title' => $this->revision->label()]));
    $form_state->setRedirect(
      'entity.vendor_record.canonical',
       ['vendor_record' => $this->revision->id()]
    );
    if ($this->connection->query('SELECT COUNT(DISTINCT vid) FROM {vendor_record_field_revision} WHERE id = :id', [':id' => $this->revision->id()])->fetchField() > 1) {
      $form_state->setRedirect(
        'entity.vendor_record.version_history',
         ['vendor_record' => $this->revision->id()]
      );
    }
  }

}
