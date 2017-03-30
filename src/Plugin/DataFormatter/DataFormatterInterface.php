<?php

namespace Drupal\audit_log\Plugin\DataFormatter;

use Drupal\audit_log\Event\AuditLogEventInterface;

/**
 * Interface description.
 */
interface DataFormatterInterface {

  /**
   * Populates the message field on the event.
   *
   * @param \Drupal\audit_log\Event\AuditLogEventInterface $event
   *   The event to be logged to the audit log.
   *
   * @throws \InvalidArgumentException
   *   Occurs if $event->getObject() is not the correct data type
   *   for the plugin.
   */
  public function format(AuditLogEventInterface $event);

}
