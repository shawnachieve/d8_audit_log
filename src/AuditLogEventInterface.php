<?php

namespace Drupal\audit_log;

use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Represents a single auditable event for logging.
 *
 * @package Drupal\audit_log
 */
interface AuditLogEventInterface {
  /**
   * Creates an instance of the AuditLogEvent.
   *
   * @param ContainerInterface $container
   *   The Drupal container dependency injection.
   * @param string $event_type
   *   The type of event being reported.
   * @param mixed $event_data
   *   The object triggering the audit log.
   *
   * @return AuditLogEventInterface
   *   An instance of the event.
   */
  public static function create(ContainerInterface $container, $event_type, $event_data);

  /**
   * Retrieves the user object for the user that triggered the event.
   *
   * @return \Drupal\Core\Session\AccountInterface
   *   The user object for the user that triggered the event.
   */
  public function getUser();

  /**
   * Retrieves the object that was modified.
   *
   * @return mixed
   *   The object that was modified.
   */
  public function getObject();

  /**
   * Retrieves the untranslated audit log message for the event.
   *
   * @return string
   *   The untranslated audit log message.
   */
  public function getMessage();

  /**
   * Retrieves the replacement tokens for the log message.
   *
   * @return array
   *   The replacement tokens for the log message.
   */
  public function getMessagePlaceholders();

  /**
   * Retrieves the type of event that was triggered.
   *
   * @return string
   *   The type of event such as "insert", "update", "delete".
   */
  public function getEventType();

  /**
   * Retrieves the original state of the object before the event occurred.
   *
   * @return string
   *   The name of the object state such as "published" or "active".
   */
  public function getPreviousState();

  /**
   * Retrieves the new state of the object after the event occurred.
   *
   * @return string
   *   The name of the object state such as "published" or "active".
   */
  public function getCurrentState();

  /**
   * The timestamp for when the event was initiated.
   *
   * @return int
   */
  public function getRequestTime();

  /**
   * Retrieves the hostname/IP address of the user triggering the event.
   *
   * @return string
   *   The IP address of the user triggering the event.
   */
  public function getHostname();

  /**
   * Sets the audit message for this event.
   *
   * @param string $message
   *   The message template for the log message.
   * @param array $variables
   *   An array of replacement tokens for the log message.
   *
   * @return AuditLogEventInterface
   *   The current instance of the event object.
   */
  public function setMessage($message, array $variables);

}
