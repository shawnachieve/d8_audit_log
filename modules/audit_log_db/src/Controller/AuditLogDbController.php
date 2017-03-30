<?php

namespace Drupal\audit_log_db\Controller;

use Drupal\Component\Utility\Html;
use Drupal\Component\Utility\Unicode;
use Drupal\Component\Utility\Xss;
use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\Database\Connection;
use Drupal\Core\Datetime\DateFormatterInterface;
use Drupal\Core\Extension\ModuleHandlerInterface;
use Drupal\Core\Form\FormBuilderInterface;
use Drupal\Core\Link;
use Drupal\Core\Logger\RfcLogLevel;
use Drupal\Core\Url;
use Drupal\Core\Utility\LinkGeneratorInterface;
use Drupal\user\Entity\User;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Returns responses for audit_log_db routes.
 */
class AuditLogDbController extends ControllerBase {

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('database'),
      $container->get('module_handler'),
      $container->get('date.formatter'),
      $container->get('form_builder')
    );
  }

  /**
   * Constructs a DbLogController object.
   *
   * @param \Drupal\Core\Database\Connection $database
   *   A database connection.
   * @param \Drupal\Core\Extension\ModuleHandlerInterface $module_handler
   *   A module handler.
   * @param \Drupal\Core\Datetime\DateFormatterInterface $date_formatter
   *   The date formatter service.
   * @param \Drupal\Core\Form\FormBuilderInterface $form_builder
   *   The form builder service.
   */
  public function __construct(Connection $database, ModuleHandlerInterface $module_handler, DateFormatterInterface $date_formatter, FormBuilderInterface $form_builder) {
    $this->database = $database;
    $this->moduleHandler = $module_handler;
    $this->dateFormatter = $date_formatter;
    $this->formBuilder = $form_builder;
    $this->userStorage = $this->entityManager()->getStorage('user');
  }

  /**
   * Displays details about a specific audit log message.
   *
   * @param int $id
   *   Unique ID of the audit log message.
   *
   * @return array
   *   If the ID is located in the Audit Logging table, a build array in the
   *   format expected by drupal_render();
   */
  public function eventDetails($id) {
    $build = array();

    $log = $this->database->select('audit_log', 'al')
      ->fields('al')
      ->condition('id', $id)
      ->execute()
      ->fetchObject();

    $user_link = new Link($log->user_name, Url::fromUserInput('/user/' . $log->user_id));

    if ($log) {
      $rows = [
        [
          ['data' => $this->t('Event Type'), 'header => TRUE'],
          $this->t($log->event_type),
        ],
        [
          ['data' => $this->t('Date'), 'header => TRUE'],
          $this->dateFormatter->format($log->timestamp, 'long'),
        ],
        [
          ['data' => $this->t('Object Type'), 'header => TRUE'],
          $log->object_type,
        ],
        [
          ['data' => $this->t('Object SubType'), 'header => TRUE'],
          $log->object_subtype,
        ],
        [
          ['data' => $this->t('Object ID'), 'header => TRUE'],
          $log->object_id,
        ],
        [
          ['data' => $this->t('Triggering User Name'), 'header => TRUE'],
          $user_link,
        ],
        [
          ['data' => $this->t('Triggering User Email'), 'header => TRUE'],
          $log->user_mail,
        ],
        [
          ['data' => $this->t('Location'), 'header => TRUE'],
          $this->l(
            $log->location,
              $log->location ?
                Url::fromUri($log->location) :
                Url::fromRoute('<none>')
          ),
        ],
        [
          ['data' => $this->t('Hostname'), 'header => TRUE'],
          $log->hostname,
        ],
        [
          ['data' => $this->t('Message'), 'header => TRUE'],
          $this->formatMessage($log),
        ],
      ];

      $build['auditlog_table'] = array(
        '#type' => 'table',
        '#rows' => $rows,
        '#attributes' => array('class' => array('auditlog-event')),
      );
    }

    return $build;
  }

  /**
   * Formats a  log message.
   *
   * @param object $row
   *   The record from the audit_log table.
   *
   * @return string|\Drupal\Core\StringTranslation\TranslatableMarkup|false
   *   The formatted log message or FALSE if the message or variables properties
   *   are not set.
   */
  public function formatMessage($row) {
    // Check for required properties.
    if (isset($row->message, $row->variables)) {
      $variables = @unserialize($row->variables);
      // Messages without variables or user specified text.
      if ($variables === NULL) {
        $message = Xss::filterAdmin($row->message);
      }
      elseif (!is_array($variables)) {
        $message = $this->t('Log data is corrupted and cannot be unserialized: @message', ['@message' => Xss::filterAdmin($row->message)]);
      }
      // Message to translate with injected variables.
      else {
        $message = $this->t(Xss::filterAdmin($row->message), $variables);
      }
    }
    else {
      $message = FALSE;
    }

    return $message;
  }
}
