<?php

namespace Drupal\audit_log\Register;

use Drupal\audit_log\AuditLogEventInterface;

/**
 * Writes audit events to a custom database table.
 *
 * @package Drupal\audit_log\Register
 */
class Database implements AuditLogRegisterInterface {

  /**
   * {@inheritdoc}
   */
  public function save(AuditLogEventInterface $event) {
    $connection = \Drupal::database();

    $entity = $event->getObject();

    $connection
      ->insert('audit_log')
      ->fields([
        'entity_id' => $entity->id(),
        'entity_type' => $entity->getEntityTypeId(),
        'user_id' => $event->getUser()->id(),
        'event' => $event->getEventType(),
        'previous_state' => $event->getPreviousState(),
        'current_state' => $event->getCurrentState(),
        'message' => $event->getMessage(),
        'variables' => serialize($event->getMessagePlaceholders()),
        'timestamp' => $event->getRequestTime(),
        'hostname' => $event->getHostname(),
      ])
      ->execute();
  }

}
