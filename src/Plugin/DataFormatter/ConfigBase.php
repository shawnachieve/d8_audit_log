<?php

namespace Drupal\audit_log\Plugin\DataFormatter;

use Drupal\audit_log\AuditLogEventInterface;
use Drupal\Core\Config\Config;


/**
 * Generic formatter for handling any kind of entity logging.
 *
 * @\Drupal\audit_log\Annotation\DataFormatter(
 *   id = "Config",
 *   label = @\Drupal\Core\Annotation\Translation("Config Data Formatter"),
 *   handles = "\Drupal\Core\Config\Config",
 *   weight = 99,
 * )
 */
class ConfigBase implements DataFormatterInterface {

  /**
   * {@inheritdoc}
   */
  public function format(AuditLogEventInterface $event) {
    $config = $event->getObject();
    if (!($config instanceof Config)) {
      throw new \InvalidArgumentException("Event object must be an instance of Config.");
    }

    $event_type = $event->getEventType();

    $message = 'Config (@name) event: @event_type';
    $args = [
      '@name' => $config->getName(),
      '@event_type' => $event_type,
    ];

    $id = $config->getName();
    $type = $config->getName();
    $subtype = '';

    $event->setMessage($message, $args);
    $event->setObjectData($id, $type, $subtype);
  }
}
