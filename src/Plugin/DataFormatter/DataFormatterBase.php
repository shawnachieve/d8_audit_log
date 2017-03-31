<?php

namespace Drupal\audit_log\Plugin\DataFormatter;

/**
 * Base class for all data formatters.
 */
abstract class DataFormatterBase implements DataFormatterInterface {

  /**
   * Recursively compares two arrays.
   *
   * @param array $array1
   *   The first array to compare.
   * @param array $array2
   *   The second array to compare.
   *
   * @return array
   *   The differences between the two arrays.
   *
   * @see http://php.net/manual/en/function.array-diff-assoc.php
   */
  protected function arrayDiffAssocRecursive(array $array1, array $array2) {
    $difference = [];
    foreach ($array1 as $key => $value) {
      if (is_array($value)) {
        if (!isset($array2[$key]) || !is_array($array2[$key])) {
          $difference[$key] = $value;
        }
        else {
          $new_diff = $this->arrayDiffAssocRecursive($value, $array2[$key]);
          if (!empty($new_diff)) {
            $difference[$key] = $new_diff;
          }
        }
      }
      elseif (!array_key_exists($key, $array2) || $array2[$key] !== $value) {
        $difference[$key] = $value;
      }
    }
    return $difference;
  }

}
