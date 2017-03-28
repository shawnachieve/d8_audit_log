<?php

namespace Drupal\audit_log\Interpreter;

use Drupal\audit_log\AuditLogEventInterface;
use Drupal\Core\Entity\EntityInterface;

/**
 * Processes node entity events.
 *
 * @package Drupal\audit_log\Interpreter
 */
class Entity implements AuditLogInterpreterInterface {

  /**
   * {@inheritdoc}
   */
  public function reactTo(AuditLogEventInterface $event) {
    $entity = $event->getObject();
    if ($entity instanceof EntityInterface) {
      $event_type = $event->getEventType();

      $message = '@name (@type) event: @event_type';
      $args = [
        '@name' => $entity->label(),
        '@type' => $entity->getEntityTypeId(),
        '@event_type' => $event_type,
      ];
      $event->setMessage($message, $args);
      return TRUE;
    }

    return FALSE;
  }

}
