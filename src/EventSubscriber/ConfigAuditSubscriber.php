<?php

namespace Drupal\audit_log\EventSubscriber;

use Drupal\audit_log\Event\AuditLogEvent;
use Drupal\Core\Config\ConfigCollectionInfo;
use Drupal\Core\Config\ConfigEvents;
use Drupal\Core\Config\ConfigImporterEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Drupal\Core\Config\ConfigCrudEvent;

/**
 * Responds to configuration change events.
 */
class ConfigAuditSubscriber implements EventSubscriberInterface {

  /**
   * {@inheritdoc}
   */
  public static function getSubscribedEvents() {
    $events = [
      ConfigEvents::DELETE => ['onConfigCrud'],
      ConfigEvents::IMPORT => ['onConfigImport'],
      ConfigEvents::IMPORT_MISSING_CONTENT => ['onConfigImport'],
      ConfigEvents::IMPORT_VALIDATE => ['onConfigImport'],
      ConfigEvents::COLLECTION_INFO => ['onCollectionInfo'],
      ConfigEvents::RENAME => ['onConfigCrud'],
      ConfigEvents::SAVE => ['onConfigCrud'],
    ];
    return $events;
  }

  /**
   * Responds to ConfigCrudEvents.
   *
   * @param \Drupal\Core\Config\ConfigCrudEvent $event
   *   The configuration CRUD event.
   * @param string $event_name
   *   The name of the event.
   */
  public function onConfigCrud(ConfigCrudEvent $event, $event_name) {
    $config = $event->getConfig();
    $audit_event = AuditLogEvent::create(\Drupal::getContainer(), $event_name, $config);
    \Drupal::service('audit_log.logger')->log($audit_event);
  }

  /**
   * Responds to Configuration Import Events.
   *
   * @param \Drupal\Core\Config\ConfigImporterEvent $event
   *   The configuration import event.
   * @param string $event_name
   *   The name of the event.
   */
  public function onConfigImport(ConfigImporterEvent $event, $event_name) {
    drupal_set_message("$event_name : Config import: ");
    $audit_event = AuditLogEvent::create(\Drupal::getContainer(), $event_name, $event);
    \Drupal::service('audit_log.logger')->log($audit_event);
  }

  /**
   * Responds to Configuration Collection Events.
   *
   * @param \Drupal\Core\Config\ConfigCollectionInfo $event
   *   The Configuration Collection event.
   * @param string $event_name
   *   The name of the event.
   */
  public function onCollectionInfo(ConfigCollectionInfo $event, $event_name) {
    drupal_set_message("$event_name : Config import-Collection Info: ");
    $audit_event = AuditLogEvent::create(\Drupal::getContainer(), $event_name, $event);
    \Drupal::service('audit_log.logger')->log($audit_event);
  }

}
