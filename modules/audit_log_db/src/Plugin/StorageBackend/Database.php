<?php

namespace Drupal\audit_log_db\Plugin\StorageBackend;

use Drupal\audit_log\AuditLogEventInterface;
use Drupal\audit_log\StorageBackendInterface;
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
    $connection = \Drupal::database();

    $entity = $event->getObject();

    $connection
      ->insert('audit_log')
      ->fields([
        'event' => $event->getEventType(),
        'object_type' => 'TBD',
        'object_subtype' => 'TBD',
        'object_id' => $entity->id(),
        'user_id' => $event->getUser()->id(),
        'message' => $event->getMessage(),
        'variables' => serialize($event->getMessagePlaceholders()),
        'timestamp' => $event->getRequestTime(),
        'hostname' => $event->getHostname(),
      ])
      ->execute();
  }

}
