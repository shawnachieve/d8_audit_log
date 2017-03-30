<?php

namespace Drupal\audit_log\Plugin;

use Drupal\Component\Plugin\Factory\DefaultFactory;
use Drupal\Core\Cache\CacheBackendInterface;
use Drupal\Core\Extension\ModuleHandlerInterface;
use Drupal\Core\Plugin\DefaultPluginManager;

/**
 * Audit Log Storage Plugin Manager.
 */
class StorageManager extends DefaultPluginManager {

  /**
   * {@inheritdoc}
   */
  public function __construct(\Traversable $namespaces, CacheBackendInterface $cache_backend, ModuleHandlerInterface $module_handler) {
    parent::__construct(
      'Plugin/StorageBackend',
      $namespaces,
      $module_handler,
      'Drupal\audit_log\StorageBackendInterface',
      'Drupal\audit_log\Annotation\BackendStorage'
    );
    $this->factory = new DefaultFactory($this->getDiscovery());
  }

}
