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

    switch ($config->getName()) {
      case 'core.extension':
        $this->processExtensionChange($event, $config);
        break;

      default:
        $message = 'Configuration changed: @name';
        $args = [
          '@name' => $config->getName(),
        ];

        $id = $config->getName();
        $type = $config->getName();

        $event->setMessage($message, $args);
        $event->setObjectData($id, $type, '');
    }


  }

  /**
   * Handles changes to modules and themes.
   *
   * @param \Drupal\audit_log\Event\AuditLogEventInterface $event
   *   The audit event being processed.
   * @param \Drupal\Core\Config\Config $config
   *   The configuration object being audited.
   */
  protected function processExtensionChange(AuditLogEventInterface $event, Config $config) {
    $orig_data = $config->getOriginal();
    $new_data = $config->getRawData();

    $module_change = count(count($new_data['module'] - $orig_data['module']));
    $theme_change = count(count($new_data['theme'] - $orig_data['theme']));

    if ($module_change < 0) {
      $subtype = 'module.uninstall';
      $diff = array_diff_assoc($new_data['module'], $orig_data['module']);

      // We need to disable logging when we are uninstalling the
      // audit_log_db submodule.  Otherwise we end up with DB Exceptions due
      // to the table not existing.
      if (array_key_exists('audit_log_db', $diff)) {
        $event->abortLogging();
      }
    }
    elseif ($module_change > 0) {
      $subtype = 'module.install';
      $diff = array_diff_assoc($orig_data['module'], $new_data['module']);
    }
    elseif ($theme_change < 0) {
      $subtype = 'theme.uninstall';
      $diff = array_diff_assoc($orig_data['theme'], $new_data['theme']);
    }
    elseif ($theme_change > 0) {
      $subtype = 'theme.install';
      $diff = array_diff_assoc($new_data['theme'], $orig_data['theme']);
    }
    else {
      $subtype = 'unknown';
      $diff = [];
    }

    $message = '@subtype : @diff';
    $args = [
      '@subtype' => $subtype,
      '@diff' => implode(', ', array_keys($diff)),
    ];

    $id = $config->getName();
    $type = $config->getName();

    $event->setMessage($message, $args);
    $event->setObjectData($id, $type, $subtype);
  }

}
