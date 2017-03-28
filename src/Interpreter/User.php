<?php

namespace Drupal\audit_log\Interpreter;

use Drupal\audit_log\AuditLogEventInterface;
use Drupal\Core\Entity\EntityInterface;
use Drupal\user\UserInterface;

/**
 * Processes User entity events.
 *
 * @package Drupal\audit_log\Interpreter
 */
class User implements AuditLogInterpreterInterface {

  /**
   * {@inheritdoc}
   */
  public function reactTo(AuditLogEventInterface $event) {
    $entity = $event->getObject();
    if ($entity instanceof EntityInterface && $entity->getEntityTypeId() == 'user') {

      $event_type = $event->getEventType();
      $args = ['@name' => $entity->label()];
      $current_state = $entity->status->value ? 'active' : 'blocked';
      $original_state = NULL;
      if (isset($entity->original) && $entity->original instanceof UserInterface) {
        $original_state = $entity->original->status->value ? 'active' : 'blocked';
      }

      $message = '@event_type User Event on @title';
      $args = ['@title' => $entity->getTitle()];
      $event->setMessage($message, $args);

      return TRUE;
    }

    return FALSE;
  }

}
