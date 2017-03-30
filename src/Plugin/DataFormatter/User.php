<?php

namespace Drupal\audit_log\Plugin\DataFormatter;

use Drupal\audit_log\Event\AuditLogEventInterface;
use Drupal\user\Entity\User as UserEntity;

/**
 * Formatter for handling user account related audits.
 *
 * @\Drupal\audit_log\Annotation\DataFormatter(
 *   id = "User",
 *   label = @\Drupal\Core\Annotation\Translation("Entity Data Formatter"),
 *   handles = "\Drupal\user\Entity\User",
 *   weight = 50,
 * )
 */
class User implements DataFormatterInterface {

  /**
   * {@inheritdoc}
   */
  public function format(AuditLogEventInterface $event) {
    $entity = $event->getObject();
    if (!($entity instanceof UserEntity)) {
      throw new \InvalidArgumentException("Event object must be an instance of User.");
    }

    $event_type = $event->getEventType();

    $message = '@event_type: @name';
    $args = [
      '@name' => $entity->label(),
      '@event_type' => $event_type,
    ];

    $id = $entity->id();
    $type = $entity->getEntityType()->id();
    $subtype = $entity->bundle();

    $event->setMessage($message, $args);
    $event->setObjectData($id, $type, $subtype);
  }

}
