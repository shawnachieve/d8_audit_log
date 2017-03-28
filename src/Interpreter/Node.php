<?php

namespace Drupal\audit_log\Interpreter;

use Drupal\audit_log\AuditLogEventInterface;
use Drupal\Core\Entity\EntityInterface;

/**
 * Processes node entity events.
 *
 * @package Drupal\audit_log\Interpreter
 */
class Node implements AuditLogInterpreterInterface {

  /**
   * {@inheritdoc}
   */
  public function reactTo(AuditLogEventInterface $event) {
    $entity = $event->getObject();
    if ($entity instanceof EntityInterface && $entity->getEntityTypeId() == 'node') {
      $event_type = $event->getEventType();
      /** @var \Drupal\node\NodeInterface $entity */
      $current_state = $entity->isPublished() ? 'published' : 'unpublished';
      $previous_state = '';
      if (isset($entity->original)) {
        $previous_state = $entity->original->isPublished() ? 'published' : 'unpublished';
      }
      $message = '@event_type Node Event on @title';
      $args = [
        '@title' => $entity->getTitle(),
        '@event_type' => $event_type,
      ];
      $event->setMessage($message, $args);

      return TRUE;
    }

    return FALSE;
  }

}
