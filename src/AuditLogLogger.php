<?php

namespace Drupal\audit_log;

use Drupal\Core\Entity\EntityInterface;
use Drupal\audit_log\Interpreter\AuditLogInterpreterInterface;

/**
 * Service for responding to audit log events.
 *
 * @package Drupal\audit_log
 */
class AuditLogLogger {
  /**
   * An array of available interpreters to respond to events.
   *
   * @var array
   */
  protected $entityEventInterpreters;

  /**
   * Logs an event to the audit log.
   *
   * @param string $event_type
   *   The type of event being reported such as "insert", "update", or "delete".
   * @param \Drupal\Core\Entity\EntityInterface $entity
   *   The entity affected during the event.
   */
  public function log($event_type, EntityInterface $entity) {
    ksort($this->entityEventInterpreters);
    $event = new AuditLogEvent();
    $account = \Drupal::service('current_user')->getAccount();
    $event->setUser($account);
    $event->setEntity($entity);
    $event->setEventType($event_type);
    $event->setRequestTime(REQUEST_TIME);

    foreach ($this->sortInterpreters() as $interpreter) {
      if ($interpreter->reactTo($event)) {
        \Drupal::service('audit_log.register')->register($event);
        break;
      }
    }

  }

  /**
   * Adds an interpreter to the processing pipeline.
   *
   * @param \Drupal\audit_log\Interpreter\AuditLogInterpreterInterface $interpreter
   *   An audit log event interpreter.
   * @param int $priority
   *   A priority specification for the interpreter.
   *
   *   Must be a positive integer.
   *
   *   Lower number interpreters are processed
   *   before higher number interpreters.
   */
  public function addInterpreter(AuditLogInterpreterInterface $interpreter, $priority = 0) {
    $this->entityEventInterpreters[$priority][] = $interpreter;
  }

  /**
   * Sorts the available interpreters by priority.
   *
   * @return array
   *   The sorted array of interpreters.
   */
  protected function sortInterpreters() {
    $sorted = [];
    krsort($this->entityEventInterpreters);

    foreach ($this->entityEventInterpreters as $entity_event_interpreters) {
      $sorted = array_merge($sorted, $entity_event_interpreters);
    }
    return $sorted;
  }

}
