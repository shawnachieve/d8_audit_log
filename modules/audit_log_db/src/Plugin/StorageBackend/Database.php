<?php

namespace Drupal\audit_log_db\Plugin\StorageBackend;

use Drupal\audit_log\Event\AuditLogEventInterface;
use Drupal\audit_log\Plugin\StorageBackend\StorageBackendInterface;
use Drupal\Component\Plugin\PluginBase;

/**
 * Writes audit events to a custom database table.
 *
 * @\Drupal\audit_log\Annotation\BackendStorage(
 *   id = "Database",
 *   label = @\Drupal\Core\Annotation\Translation("Database Storage"),
 * )
 *
 * @package Drupal\audit_log\audit_log_db\StorageBackend
 */
class Database extends PluginBase implements StorageBackendInterface {

  /**
   * {@inheritdoc}
   */
  public function save(AuditLogEventInterface $event) {
    // This check is a workaround so that we do not try to log events
    // while this module is being installed or uninstalled.
    $installed = \Drupal::state()->get('audit_log_db_installed');
    if ($installed !== TRUE) {
      return;
    }

    $connection = \Drupal::database();
    if (!$connection->schema()->tableExists('audit_log')) {
      drupal_set_message('audit_log table does not exist', 'error');
      return;
    }

    $connection
      ->insert('audit_log')
      ->fields([
        'event_type' => $event->getEventType(),
        'object_type' => $event->getObjectType(),
        'object_subtype' => $event->getObjectSubType(),
        'object_id' => $event->getObjectId(),
        'user_id' => $event->getAccountId(),
        'user_name' => $event->getAccountUsername(),
        'user_mail' => $event->getAccountMail(),
        'message' => $event->getMessage(),
        'variables' => serialize($event->getMessagePlaceholders()),
        'timestamp' => $event->getRequestTime(),
        'hostname' => $event->getHostname(),
        'location' => $event->getLocation(),
      ])
      ->execute();
  }

}
