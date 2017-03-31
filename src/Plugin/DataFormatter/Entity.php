<?php

namespace Drupal\audit_log\Plugin\DataFormatter;

use Drupal\audit_log\Event\AuditLogEventInterface;
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
class Entity extends DataFormatterBase {

  /**
   * {@inheritdoc}
   */
  public function format(AuditLogEventInterface $event) {
    $entity = $event->getObject();
    if (!($entity instanceof EntityInterface)) {
      throw new \InvalidArgumentException(
        'Event object must be an instance of EntityInterface.'
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
      '@type' => $entity->getEntityTypeId(),
      '@event_type' => $event_type,
      '@diff' => print_r($diff, TRUE),
    ];

    $id = $entity->id();
    $type = $entity->getEntityType()->id();
    $subtype = $entity->bundle();

    $event->setMessage($message, $args);
    $event->setObjectData($id, $type, $subtype);
  }

  /**
   * Retrieves a list of changes to this entity.
   *
   * @param \Drupal\Core\Entity\EntityInterface $entity
   *   The entity being audited.
   *
   * @return array
   *   An array of changes to the entity.  An empty array if no changes.
   */
  protected function getChanges(EntityInterface $entity) {
    $cur_data = $entity->toArray();
    if ($entity->isNew()) {
      return $cur_data;
    }

    $orig_data = isset($entity->original) ? $entity->original->toArray() : [];
    if (isset($cur_data['original'])) {
      unset($cur_data['original']);
    }

    $diff = $this->arrayDiffAssocRecursive($cur_data, $orig_data);

    return $diff;
  }

}
