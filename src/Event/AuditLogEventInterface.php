<?php

namespace Drupal\audit_log\Event;

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
   * Flag this event as not-logged.
   */
  public function abortLogging();

  /**
   * Indicates if this event can be logged or not.
   *
   * @return bool
   *   TRUE if the event should be written to the log.
   *   FALSE if the event should not be written to the log.
   */
  public function isLoggable();

  /**
   * Retrieves the user object for the user that triggered the event.
   *
   * @return \Drupal\Core\Session\AccountInterface
   *   The user object for the user that triggered the event.
   */
  public function getAccount();

  /**
   * The ID of the user account triggering the audit event.
   *
   * @return int
   *   The internal ID of the user account.
   */
  public function getAccountId();

  /**
   * The email address of the user triggering the audit event.
   *
   * @return string
   *   The email of the user account.
   */
  public function getAccountMail();

  /**
   * The username of the user triggering the audit event.
   *
   * @return string
   *   The username of the user account.
   */
  public function getAccountUsername();

  /**
   * Retrieves the object that was modified.
   *
   * @return mixed
   *   The object that was modified.
   */
  public function getObject();

  /**
   * Retrieves the ID of the object being audited.
   *
   * @return string
   *   The ID of the object.
   */
  public function getObjectId();

  /**
   * Retrieves the type of the object such as 'user' or 'node'.
   *
   * @return string
   *   The object type.
   */
  public function getObjectType();

  /**
   * Retrieves the subtype of the object such as 'page' or 'article'.
   *
   * @return string
   *   The object subtype.
   */
  public function getObjectSubType();

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
   * Retrieves the URL of the page that triggered the audit event.
   *
   * @return string
   *   URL of the request page.
   */
  public function getLocation();

  /**
   * Indicates if the event was triggered from drush or another CLI tool.
   *
   * @return bool
   *   TRUE if the event was triggered by a CLI command.  Otherwise FALSE.
   */
  public function isCliSource();

  /**
   * Determines if the event has been processed by a formatter.
   *
   * @return bool
   *   TRUE if the event has been processed and is ready for logging.
   */
  public function isFormattedForLogging();

  /**
   * Sets the audit message for this event.
   *
   * @param string $message
   *   The message template for the log message.
   * @param array $variables
   *   An array of replacement tokens for the log message.
   *
   * @return \Drupal\audit_log\AuditLogEventInterface
   *   The current instance of the event object.
   */
  public function setMessage($message, array $variables);

  /**
   * Stores information about the object being audited.
   *
   * @param string|int $id
   *   The unique name or ID for the object.
   * @param string $type
   *   The base type for the object such as 'entity', 'node' or 'configuration'.
   * @param string $subtype
   *   The subtype or bundle for the object such as 'article'.
   *
   * @return \Drupal\audit_log\AuditLogEventInterface
   *   The current instance of the event object.
   */
  public function setObjectData($id, $type, $subtype = '');

}
