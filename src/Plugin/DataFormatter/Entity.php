<?php

namespace Drupal\audit_log\Plugin\DataFormatter;

use Drupal\audit_log\AuditLogEventInterface;
use Drupal\Core\Entity\EntityInterface;


/**
 * Generic formatter for handling any kind of entity logging.
 *
 * @\Drupal\audit_log\Annotation\DataFormatter(
 *   id = "Entity",
 *   label = @\Drupal\Core\Annotation\Translation("Entity Data Formatter"),
 *   handles = "\Drupal\Core\Entity\EntityInterface",
 *   weight = 99,
 * )
 */
class Entity implements DataFormatterInterface {

  /**
   * {@inheritdoc}
   */
  public function format(AuditLogEventInterface $event) {
    $entity = $event->getObject();
    if (!($entity instanceof EntityInterface)) {
      throw new \InvalidArgumentException("Event object must be an instance of EntityInterface.");
    }

    $event_type = $event->getEventType();

    $message = '@name (@type) event: @event_type';
    $args = [
      '@name' => $entity->label(),
      '@type' => $entity->getEntityTypeId(),
      '@event_type' => $event_type,
    ];

    $id = $entity->id();
    $type = $entity->getEntityType()->id();
    $subtype = $entity->bundle();

    $event->setMessage($message, $args);
    $event->setObjectData($id, $type, $subtype);
  }

}
