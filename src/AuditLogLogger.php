<?php

namespace Drupal\audit_log;

/**
 * Service for responding to audit log events.
 *
 * @package Drupal\audit_log
 */
class AuditLogLogger {

  /**
   * Logs an event to the audit log.
   *
   * @param AuditLogEventInterface $event
   *   The event to be written to the audit log.
   */
  public function log(AuditLogEventInterface $event) {
    $this->formatData($event);
    if ($event->isFormattedForLogging()) {
      $this->writeLog($event);
    }
  }

  /**
   * Adds object specific data to the event object.
   *
   * @param \Drupal\audit_log\AuditLogEventInterface $event
   *   The audit event data.
   */
  protected function formatData(AuditLogEventInterface $event) {
    /** @var \Drupal\audit_log\DataFormatterManager $manager */
    $manager = \Drupal::service('audit_log.manager.formatter');
    /** @var \Drupal\audit_log\Plugin\DataFormatter\DataFormatterInterface $plugin */
    $plugin = $manager->getPluginForType($event->getObject());
    if ($plugin) {
      $plugin->format($event);
    }
  }

  /**
   * Writes the event to all available logging storage backends.
   *
   * @param \Drupal\audit_log\AuditLogEventInterface $event
   *   The audit event data.
   */
  protected function writeLog(AuditLogEventInterface $event) {
    /** @var \Drupal\audit_log\StorageManager $manager */
    $manager = \Drupal::service('audit_log.manager.storage');
    $plugins = $manager->getDefinitions();
    foreach ($plugins as $plugin) {
      /** @var \Drupal\audit_log\StorageBackendInterface $instance */
      $instance = $manager->createInstance($plugin['id']);
      $instance->save($event);
    }
  }

}
