<?php

namespace Drupal\audit_log;

/**
 * Defines a Storage Backend for the audit log module.
 */
interface StorageBackendInterface {

  /**
   * Writes the event to the backend storage system.
   *
   * @param AuditLogEventInterface $event
   *   The audit event to be stored.
   */
  public function save(AuditLogEventInterface $event);

}
