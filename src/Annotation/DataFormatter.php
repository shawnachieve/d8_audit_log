<?php

namespace Drupal\audit_log\Annotation;

use Drupal\Component\Annotation\Plugin;

/**
 * Defines an audit log data formatter plugin.
 *
 * @Annotation
 */
class DataFormatter extends Plugin {
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

  /**
   * A fully qualified type name (or interface) this plugin formats data for.
   *
   * @var string
   */
  public $handles;

  /**
   * The weight or priority for this formatter to process requests.
   *
   * Lower weights are processed first.
   *
   * @var int
   */
  public $weight = 0;

}
