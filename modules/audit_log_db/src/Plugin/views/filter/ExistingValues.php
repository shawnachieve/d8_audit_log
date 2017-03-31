<?php

namespace Drupal\audit_log_db\Plugin\views\filter;

use Drupal\views\Plugin\views\filter\InOperator;

/**
 * Allows filtering based on a distinct set of values in the DB.
 *
 * @ingroup views_filter_handlers
 *
 * @ViewsFilter("audit_log_db_values")
 */
class ExistingValues extends InOperator {

  /**
   * {@inheritdoc}
   */
  public function getValueOptions() {
    $this->valueOptions = $this->getDistinctFieldValues();
  }

  /**
   * Retrieves a list of distinct values for the current field from the DB.
   *
   * @return array
   *   An array of distinct field values.
   */
  protected function getDistinctFieldValues() {
    $real_field = $this->realField;
    $values = [];

    // TODO: Add some caching for this info.
    $connection = \Drupal::database();
    $rows = $connection->select('audit_log', 'al')
      ->fields('al', [$real_field])
      ->distinct(TRUE)
      ->orderBy('al.' . $real_field)
      ->execute();

    foreach ($rows as $row) {
      $values[$row->{$real_field}] = $row->{$real_field};
    }

    return $values;
  }

}
