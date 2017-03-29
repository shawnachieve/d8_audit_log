<?php

namespace Drupal\audit_log\Annotation;

use Drupal\Component\Annotation\Plugin;

/**
 * Defines an annotation for an Audit Log Backend Storage Plugin.
 *
 * @Annotation
 */
class BackendStorage extends Plugin {
  /**
   * The plugin ID.
   *
   * @var string
   */
  public $id;

  /**
   * The human-readable name of the backend.
   *
   * @var \Drupal\Core\Annotation\Translation
   *
   * @ingroup plugin_translatable
   */
  public $label;

}
