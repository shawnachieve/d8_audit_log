<?php

namespace Drupal\audit_log\EventSubscriber;

use Drupal\audit_log\AuditLogEvent;
use Drupal\Core\Config\ConfigCollectionInfo;
use Drupal\Core\Config\ConfigEvents;
use Drupal\Core\Config\ConfigImporterEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Drupal\Core\Config\ConfigCrudEvent;

/**
 * Class description.
 */
class ConfigAuditSubscriber implements EventSubscriberInterface  {

  /**
   * {@inheritdoc}
   */
  public static function getSubscribedEvents() {
//    $events = [
//      ConfigEvents::DELETE => ['onDelete'],
//      ConfigEvents::IMPORT => ['onImport'],
//      ConfigEvents::IMPORT_MISSING_CONTENT => ['onImportMissing'],
//      ConfigEvents::IMPORT_VALIDATE => ['onImportValidate'],
//      ConfigEvents::COLLECTION_INFO => ['onCollectionInfo'],
//      ConfigEvents::RENAME => ['onRename'],
//      ConfigEvents::SAVE => ['onSave'],
//    ];
    $events = [
      ConfigEvents::DELETE => ['onEventTrack'],
      ConfigEvents::IMPORT => ['onConfigImport'],
      ConfigEvents::IMPORT_MISSING_CONTENT => ['onConfigImport'],
      ConfigEvents::IMPORT_VALIDATE => ['onConfigImport'],
      ConfigEvents::COLLECTION_INFO => ['onCollectionInfo'],
      ConfigEvents::RENAME => ['onEventTrack'],
      ConfigEvents::SAVE => ['onEventTrack'],
    ];
    return $events;
  }

  /**
   * @param \Drupal\Core\Config\ConfigCrudEvent $event
   */
  public function onEventTrack(ConfigCrudEvent $event, $event_name) {
    $args = func_get_args();
    $config = $event->getConfig();
    $orig_data = $config->getOriginal();
    $new_data = $config->getRawData();
    $config_name = $config->getName();
    drupal_set_message("$event_name : Config changed: " . $config_name);

    $audit_event = AuditLogEvent::create(\Drupal::getContainer(), $event_name, $event);
    \Drupal::service('audit_log.logger')->log($audit_event);
  }

  /**
   * @param \Drupal\Core\Config\ConfigImporterEvent $event
   * @param $event_name
   */
  public function onConfigImport(ConfigImporterEvent $event, $event_name) {
    $args = func_get_args();
    drupal_set_message("$event_name : Config import: ");
    $audit_event = AuditLogEvent::create(\Drupal::getContainer(), $event_name, $event);
    \Drupal::service('audit_log.logger')->log($audit_event);
  }

  public function onCollectionInfo(ConfigCollectionInfo $event, $event_name) {
    $args = func_get_args();
    drupal_set_message("$event_name : Config import-Collection Info: ");
    $audit_event = AuditLogEvent::create(\Drupal::getContainer(), $event_name, $event);
    \Drupal::service('audit_log.logger')->log($audit_event);
  }
}
