<?php

namespace Drupal\audit_log\Event;

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
   * The user account object that triggered the audit event.
   *
   * @var \Drupal\user\UserInterface
   */
  protected $account;

  /**
   * The ID of the user that triggered the audit event.
   *
   * @var int
   */
  protected $accountId;

  /**
   * The username of the user that triggered the audit event.
   *
   * @var string
   */
  protected $accountUsername;

  /**
   * The email of the user that triggered the audit event.
   *
   * @var string
   */
  protected $accountMail;

  /**
   * The object being modified.
   *
   * @var mixed
   */
  protected $object;

  /**
   * The ID of the object being audited.
   *
   * @var string
   */
  protected $objectId;

  /**
   * The type of object being audited.
   *
   * @var string
   */
  protected $objectType;

  /**
   * The subtype or bundle of object being audited.
   *
   * @var string
   */
  protected $objectSubType;

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
   * The URL of the page generating the audit event.
   *
   * @var string
   */
  protected $location;

  /**
   * Indicates if the event was triggered by a CLI action.
   *
   * @var bool
   */
  protected $cli;

  /**
   * Indicates if the event can be written to the log.
   *
   * @var bool
   */
  protected $loggable = TRUE;

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
   * @param string $location
   *   The URL of the page generating the event.
   * @param bool $cli
   *   Indicates if this event was triggered via drush or other CLI tool.
   */
  protected function __construct(
    $event_type,
    AccountInterface $account,
    $source,
    $hostname,
    $location,
    $cli = FALSE
  ) {
    $this->eventType = $event_type;
    $this->account = $account;
    $this->object = $source;
    $this->hostname = $hostname;
    $this->location = $location;
    $this->cli = $cli;

    if ($cli && (empty($account) || empty($account->getAccountName()))) {
      $this->accountUsername = 'CLI';
      $this->accountId = 0;
      $this->accountMail = '';
    }
    else {
      $this->accountUsername = $account->getAccountName();
      $this->accountMail = $account->getEmail();
      $this->accountId = $account->id();
    }
  }

  /**
   * {@inheritdoc}
   */
  public static function create(
    ContainerInterface $container,
    $event_type,
    $event_data
  ) {
    if (!\Drupal::hasContainer()) {
      \Drupal::setContainer($container);
    }
    $account = \Drupal::currentUser()->getAccount();
    $client_ip = \Drupal::request()->getClientIp();
    $hostname = Unicode::substr($client_ip, 0, 128);

    // See if the event was triggered via the CLI rather than HTTP.
    $cli = (php_sapi_name() == 'cli');
    if ($cli && isset($GLOBALS['argv'])) {
      $drush_args = $GLOBALS['argv'];
      $location = implode(' ', $drush_args);
      if (isset($_SERVER['USER'])) {
        $shell_user = $_SERVER['USER'];
        $location = '[Run As: ' . $shell_user . '] ' . $location;
      }
    }
    else {
      $location = \Drupal::request()->getUri();
    }
    return new static(
      $event_type,
        $account,
        $event_data,
        $hostname,
        $location,
        $cli
    );
  }

  /**
   * {@inheritdoc}
   */
  public function abortLogging() {
    $this->loggable = FALSE;
  }

  /**
   * {@inheritdoc}
   */
  public function getAccount() {
    return $this->account;
  }

  /**
   * {@inheritdoc}
   */
  public function getAccountId() {
    return $this->accountId;
  }

  /**
   * {@inheritdoc}
   */
  public function getAccountMail() {
    return $this->accountMail;
  }

  /**
   * {@inheritdoc}
   */
  public function getAccountUsername() {
    return $this->accountUsername;
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
  public function getObjectId() {
    return $this->objectId;
  }

  /**
   * {@inheritdoc}
   */
  public function getObjectType() {
    return $this->objectType;
  }

  /**
   * {@inheritdoc}
   */
  public function getObjectSubType() {
    return $this->objectSubType;
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
  public function getLocation() {
    return $this->location;
  }

  /**
   * {@inheritdoc}
   */
  public function isCliSource() {
    return $this->cli;
  }

  /**
   * {@inheritdoc}
   */
  public function isLoggable() {
    return $this->loggable;
  }

  /**
   * {@inheritdoc}
   */
  public function isFormattedForLogging() {
    $formatted = (
      !empty($this->objectId) &&
      !empty($this->objectType) &&
      !empty($this->message)
    );

    return $formatted;
  }

  /**
   * {@inheritdoc}
   */
  public function setMessage($message, array $variables) {
    $this->message = $message;
    $this->messagePlaceholders = $variables;
  }

  /**
   * {@inheritdoc}
   */
  public function setObjectData($id, $type, $subtype = '') {
    $this->objectId = $id;
    $this->objectType = $type;
    $this->objectSubType = $subtype;
  }

}
