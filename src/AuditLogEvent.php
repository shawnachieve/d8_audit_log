<?php

namespace Drupal\audit_log;

use Drupal\Component\Utility\Unicode;
use Drupal\Core\Session\AccountInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Represents a single auditable event for logging.
 *
 * @package Drupal\audit_log
 */
class AuditLogEvent implements AuditLogEventInterface {
  /**
   * The user that triggered the audit event.
   *
   * @var \Drupal\user\UserInterface
   */
  protected $user;

  /**
   * The object being modified.
   *
   * @var mixed
   */
  protected $object;

  /**
   * The audit message to write to the log.
   *
   * @var string
   */
  protected $message;

  /**
   * Array of variables that match the message string replacement tokens.
   *
   * @var array
   */
  protected $messagePlaceholders = [];

  /**
   * The type of event being reported.
   *
   * @var string
   */
  protected $eventType;

  /**
   * The original state of the object before the event occurred.
   *
   * @var string
   */
  protected $previousState;

  /**
   * The new state of the object after the event occurred.
   *
   * @var string
   */
  protected $currentState;

  /**
   * Timestamp for when the event occurred.
   *
   * @var int
   */
  protected $requestTime = REQUEST_TIME;

  /**
   * The hostname IP address of the user triggering the event.
   *
   * @var string
   */
  protected $hostname;

  /**
   * AuditLogEvent constructor.
   *
   * @param string $event_type
   *   The type of event being audited.
   * @param \Drupal\Core\Session\AccountInterface $account
   *   The user account making the change.
   * @param mixed $source
   *   The object being audited.
   * @param string $hostname
   *   The hostname of the user making the change.
   */
  protected function __construct($event_type, $account, $source, $hostname) {
    $this->eventType = $event_type;
    $this->user = $account;
    $this->object = $source;
    $this->hostname = $hostname;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container, $event_type, $event_data) {
    if (!\Drupal::hasContainer()) {
      \Drupal::setContainer($container);
    }
    $account = \Drupal::currentUser()->getAccount();
    $client_ip = \Drupal::request()->getClientIp();
    $hostname = Unicode::substr($client_ip, 0, 128);
    return new static($event_type, $account, $event_data, $hostname);
  }

  /**
   * {@inheritdoc}
   */
  public function setRequestTime($request_time) {
    $this->requestTime = $request_time;
    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public function setHostname($hostname) {
    $this->hostname = $hostname;
    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public function getUser() {
    return $this->user;
  }

  /**
   * {@inheritdoc}
   */
  public function getObject() {
    return $this->object;
  }

  /**
   * {@inheritdoc}
   */
  public function getMessage() {
    return $this->message;
  }

  /**
   * {@inheritdoc}
   */
  public function getMessagePlaceholders() {
    return $this->messagePlaceholders;
  }

  /**
   * {@inheritdoc}
   */
  public function getEventType() {
    return $this->eventType;
  }

  /**
   * {@inheritdoc}
   */
  public function getPreviousState() {
    return $this->previousState;
  }

  /**
   * {@inheritdoc}
   */
  public function getCurrentState() {
    return $this->currentState;
  }

  /**
   * {@inheritdoc}
   */
  public function getRequestTime() {
    return $this->requestTime;
  }

  /**
   * {@inheritdoc}
   */
  public function getHostname() {
    return $this->hostname;
  }

  /**
   * {@inheritdoc}
   */
  public function setMessage($message, array $variables) {
    $this->message = $message;
    $this->messagePlaceholders = $variables;
  }

}
