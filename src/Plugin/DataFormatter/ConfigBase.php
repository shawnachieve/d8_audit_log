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
class ConfigBase extends DataFormatterBase {

  /**
   * {@inheritdoc}
   */
  public function format(AuditLogEventInterface $event) {
    $config = $event->getObject();
    if (!($config instanceof Config)) {
      throw new \InvalidArgumentException(
        'Event object must be an instance of Config.'
      );
    }

    // No changes, so nothing to log.
    $diff = $this->configChanged($config);
    if (!$diff) {
      $event->abortLogging();
      return;
    }

    switch ($config->getName()) {
      case 'core.extension':
        $this->processExtensionChange($event, $config);
        break;

      default:
        $this->processGenericUpdate($event, $config, $diff);
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
  protected function processExtensionChange(
    AuditLogEventInterface $event,
    Config $config
  ) {
    $new_data = $config->getRawData();
    $orig_data = $config->getOriginal();

    $module_change = 0;
    if (isset($new_data['module']) && isset($orig_data['module'])) {
      $module_change = count($new_data['module']) - count($orig_data['module']);
    }
    $theme_change = 0;
    if (isset($new_data['theme']) && isset($orig_data['theme'])) {
      $theme_change = count($new_data['theme']) - count($orig_data['theme']);
    }
    // Nothing has changes or we're in the install process of this module.
    if ($module_change === 0 && $theme_change === 0) {
      $event->abortLogging();
      return;
    }

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

  /**
   * Determines if the configuration objects have changed.
   *
   * @param \Drupal\Core\Config\Config $config
   *   The configuration object being audited.
   *
   * @return array
   *   An array of differences in the config.
   */
  protected function configChanged(Config $config) {
    // New is always considered a change.
    $new_data = $config->getRawData();
    if ($config->isNew()) {
      return $new_data;
    }
    $orig_data = $config->getOriginal();

    // Step 1: Check the hash, it will be different if the config has changed.
    $orig_hash = isset($orig_data['_core']['default_config_hash'])
      ? $orig_data['_core']['default_config_hash']
      : NULL;
    $new_hash = isset($new_data['_core']['default_config_hash'])
      ? $new_data['_core']['default_config_hash']
      : NULL;
    if (!empty($orig_hash) && !empty($new_hash) && $orig_hash != $new_hash) {
      return [];
    }

    // Step 2: Do a recursive scan of the configs looking for differences.
    $diff = $this->arrayDiffAssocRecursive($new_data, $orig_data);
    if (!empty($diff)) {
      return $diff;
    }

    return [];
  }

  /**
   * Generic formatter for any unhandled configuration.
   *
   * @param \Drupal\audit_log\Event\AuditLogEventInterface $event
   *   The audit event being processed.
   * @param \Drupal\Core\Config\Config $config
   *   The configuration object being audited.
   * @param array $diff
   *   The changes made to the configuration.
   */
  protected function processGenericUpdate(
    AuditLogEventInterface $event,
    Config $config,
    array $diff
  ) {
    $message = 'Configuration changed: @name; diff: @diff';
    $args = [
      '@name' => $config->getName(),
      '@diff' => print_r($diff, TRUE),
    ];

    $id = $config->getName();
    $type = $config->getName();

    $event->setMessage($message, $args);
    $event->setObjectData($id, $type, '');
  }

}
