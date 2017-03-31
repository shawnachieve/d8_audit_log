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
class User extends Entity {

  /**
   * {@inheritdoc}
   */
  public function format(AuditLogEventInterface $event) {
    $entity = $event->getObject();
    if (!($entity instanceof UserEntity)) {
      throw new \InvalidArgumentException(
        'Event object must be an instance of User.'
      );
    }

    // Skip processing if there are no changes.
    $diff = $this->getChanges($entity);
    if (empty($diff)) {
      $event->abortLogging();
      return;
    }

    $event_type = $event->getEventType();

    $message = '@event_type: @name; Changes: @diff';
    $args = [
      '@name' => $entity->label(),
      '@event_type' => $event_type,
      '@diff' => print_r($diff, TRUE),
    ];

    $id = $entity->id();
    $type = $entity->getEntityType()->id();
    $subtype = $entity->bundle();

    $event->setMessage($message, $args);
    $event->setObjectData($id, $type, $subtype);
  }

}
