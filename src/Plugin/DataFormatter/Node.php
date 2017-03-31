<?php

namespace Drupal\audit_log\Plugin\DataFormatter;

use Drupal\audit_log\Event\AuditLogEventInterface;
use Drupal\node\Entity\Node as NodeEntity;

/**
 * Formatter for handling node related audits.
 *
 * @\Drupal\audit_log\Annotation\DataFormatter(
 *   id = "Node",
 *   label = @\Drupal\Core\Annotation\Translation("Entity Data Formatter"),
 *   handles = "\Drupal\node\Entity\Node",
 *   weight = 50,
 * )
 */
class Node extends Entity {

  /**
   * {@inheritdoc}
   */
  public function format(AuditLogEventInterface $event) {
    $entity = $event->getObject();
    if (!($entity instanceof NodeEntity)) {
      throw new \InvalidArgumentException(
        'Event object must be an instance of Node.'
      );
    }

    // Skip processing if there are no changes.
    $diff = $this->getChanges($entity);
    if (empty($diff)) {
      $event->abortLogging();
      return;
    }

    // Tell the event the general metadata about this node.
    $id = $entity->id();
    $type = $entity->getEntityType()->id();
    $subtype = $entity->bundle();
    $event->setObjectData($id, $type, $subtype);

    $event_type = $event->getEventType();
    $message = '@event_type: Content @title was modified; Changes: @diff';
    $args = [
      '@title' => $entity->label(),
      '@event_type' => $event_type,
      '@diff' => print_r($diff, TRUE),
    ];
    if ($entity->isNew()) {
      $message = 'New node created: @title; Changes: @diff';
    }
    elseif ($entity->isNewRevision()) {
      $message = 'New node revision created: @title; Changes: @diff';
    }
    elseif ($entity->isNewTranslation()) {
      $message = 'New node translation created: @title; Changes: @diff';
    }

    if ($event_type == 'entity.delete') {
      $message = 'Node was deleted: @title';
    }
    if (isset($diff['moderation_state'][0]['target_id'])) {
      $message = 'Node was transitioned to @state: @title; Changes: @diff';
      $args['@state'] = $diff['moderation_state'][0]['target_id'];
    }

    $event->setMessage($message, $args);
  }

}
