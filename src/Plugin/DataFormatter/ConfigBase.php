<?php

namespace Drupal\audit_log\Plugin\DataFormatter;

use Drupal\audit_log\Event\AuditLogEventInterface;
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
    $orig_data = $config->getOriginal();
    $new_data = $config->getRawData();

    $subtype = '';
    switch ($config->getName()) {
      case 'core.extension':
        $diff = array_diff_assoc($orig_data['module'], $new_data['module']);
        $subtype = 'module.install';
        if (stripos($event->getLocation(), '/admin/modules/uninstall/confirm') !== FALSE) {
          $subtype = 'module.uninstall';
        }
        elseif (stripos($event->getLocation(), '/drush') !== FALSE) {
          if (stripos($event->getLocation(), ' pm-uninstall ') !== FALSE
            || stripos($event->getLocation(), ' pmu ') !== FALSE) {
            $subtype = 'module.uninstall';
          }
        }
        break;

      default:
        $diff = array_diff_assoc($orig_data, $new_data);
    }

    // TODO: Need separate formatters based on the config object.
    $message = 'Config (@name) event: @event_type changes: @diff';
    $args = [
      '@name' => $config->getName(),
      '@event_type' => $event_type,
      '@diff' => print_r($diff, TRUE),
    ];

    $id = $config->getName();
    $type = $config->getName();

    $event->setMessage($message, $args);
    $event->setObjectData($id, $type, $subtype);
  }

}
