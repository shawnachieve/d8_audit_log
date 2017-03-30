<?php

namespace Drupal\audit_log\Plugin;

use Drupal\Component\Plugin\Factory\DefaultFactory;
use Drupal\Core\Cache\CacheBackendInterface;
use Drupal\Core\Extension\ModuleHandlerInterface;
use Drupal\Core\Plugin\DefaultPluginManager;

/**
 * Audit Log Data Formatter Plugin Manager.
 */
class DataFormatterManager extends DefaultPluginManager {
  /**
   * Weighted map of available plugins and the types they process.
   *
   * @var array
   */
  protected $map;

  /**
   * {@inheritdoc}
   */
  public function __construct(\Traversable $namespaces, CacheBackendInterface $cache_backend, ModuleHandlerInterface $module_handler) {
    parent::__construct(
      'Plugin/DataFormatter',
      $namespaces,
      $module_handler,
      'Drupal\audit_log\DataFormatterInterface',
      'Drupal\audit_log\Annotation\DataFormatter'
    );
    $this->factory = new DefaultFactory($this->getDiscovery());

    $plugins = $this->getDefinitions();
    foreach ($plugins as $id => $definition) {
      $weight = $definition['weight'];
      $handles = $definition['handles'];
      $this->map[$weight][$handles] = $id;
    }
    ksort($this->map);
  }

  /**
   * Retrieves a data formatter plugin instance for the event object.
   *
   * @param mixed $object
   *   The object triggering the audit event.
   *
   * @return \Drupal\audit_log\Plugin\DataFormatter\DataFormatterInterface
   *   An instance of the DataFormatter Plugin to handle the object.
   */
  public function getPluginForType($object) {
    $match = NULL;
    foreach ($this->map as $items) {
      foreach ($items as $type_name => $plugin_name) {
        if ($object instanceof $type_name) {
          $match = $plugin_name;
          break 2;
        }
      }
    }

    $instance = NULL;
    if (!empty($match)) {
      /** @var \Drupal\audit_log\Plugin\DataFormatter\DataFormatterInterface $instance */
      $instance = $this->createInstance($match);
    }

    return $instance;
  }

}
