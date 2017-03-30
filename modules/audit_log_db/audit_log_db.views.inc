<?php

/**
 * @file
 * Provide views integration for audit_log_db.module.
 */

/**
 * Implements hook_views_data().
 */
function audit_log_db_views_data() {
  $data = [];
  $data['audit_log'] = [];
  $data['audit_log']['table'] = [];

  $data['audit_log']['table']['group'] = t('Audit Log');

  $data['audit_log']['table']['base'] = [
    'field' => 'id',
    'title' => t('Audit Log entries'),
    'help' => t('Contains a list of audit log entries.'),
  ];

  $data['audit_log']['id'] = [
    'title' => t('ID'),
    'help' => t('Unique audit log event ID.'),
    'field' => [
      'id' => 'numeric',
    ],
    'filter' => [
      'id' => 'numeric',
    ],
    'argument' => [
      'id' => 'numeric',
    ],
    'sort' => [
      'id' => 'standard',
    ],
  ];

  $data['audit_log']['event_type'] = [
    'title' => t('Event Type'),
    'help' => t('The type of the event, for example "entity.insert".'),
    'field' => [
      'id' => 'standard',
    ],
    'argument' => [
      'id' => 'string',
    ],
    'filter' => [
      'id' => 'string',
    ],
    'sort' => [
      'id' => 'standard',
    ],
  ];

  $data['audit_log']['object_type'] = [
    'title' => t('Object Type'),
    'help' => t('The type of the object being audited, for example "node" or "user".'),
    'field' => [
      'id' => 'standard',
    ],
    'argument' => [
      'id' => 'string',
    ],
    'filter' => [
      'id' => 'string',
    ],
    'sort' => [
      'id' => 'standard',
    ],
  ];

  $data['audit_log']['object_subtype'] = [
    'title' => t('Object SubType'),
    'help' => t('The subtype or bundle of the entity being audited, for example "page".'),
    'field' => [
      'id' => 'standard',
    ],
    'argument' => [
      'id' => 'string',
    ],
    'filter' => [
      'id' => 'string',
    ],
    'sort' => [
      'id' => 'standard',
    ],
  ];

  $data['audit_log']['object_id'] = [
    'title' => t('Object ID'),
    'help' => t('The ID of the object being audited.'),
    'field' => [
      'id' => 'standard',
    ],
    'filter' => [
      'id' => 'string',
    ],
    'argument' => [
      'id' => 'string',
    ],
  ];

  $data['audit_log']['user_id'] = [
    'title' => t('UID'),
    'help' => t('The user ID of the user on which the log entry was written..'),
    'field' => [
      'id' => 'numeric',
    ],
    'filter' => [
      'id' => 'numeric',
    ],
    'argument' => [
      'id' => 'numeric',
    ],
    'relationship' => [
      'title' => t('User'),
      'help' => t('The user on which the log entry as written.'),
      'base' => 'users',
      'base field' => 'uid',
      'id' => 'standard',
    ],
  ];

  $data['audit_log']['user_name'] = [
    'title' => t('User: Name'),
    'help' => t('The username of the user at the time of the event.'),
    'field' => [
      'id' => 'standard',
    ],
    'argument' => [
      'id' => 'string',
    ],
    'filter' => [
      'id' => 'string',
    ],
    'sort' => [
      'id' => 'standard',
    ],
  ];

  $data['audit_log']['user_mail'] = [
    'title' => t('User: Email'),
    'help' => t('The email of the user at the time of the event.'),
    'field' => [
      'id' => 'standard',
    ],
    'argument' => [
      'id' => 'string',
    ],
    'filter' => [
      'id' => 'string',
    ],
    'sort' => [
      'id' => 'standard',
    ],
  ];

  $data['audit_log']['message'] = [
    'title' => t('Message'),
    'help' => t('The actual message of the log entry.'),
    'field' => [
      'id' => 'audit_log_db_message',
    ],
    'argument' => [
      'id' => 'string',
    ],
    'filter' => [
      'id' => 'string',
    ],
    'sort' => [
      'id' => 'standard',
    ],
  ];

  $data['audit_log']['variables'] = [
    'title' => t('Variables'),
    'help' => t('The token replacements for the message.'),
    'field' => [
      'id' => 'standard',
    ],
    'argument' => [
      'id' => 'string',
    ],
    'filter' => [
      'id' => 'string',
    ],
    'sort' => [
      'id' => 'standard',
    ],
  ];

  $data['audit_log']['timestamp'] = [
    'title' => t('Timestamp'),
    'help' => t('Date when the event occurred.'),
    'field' => [
      'id' => 'date',
    ],
    'argument' => [
      'id' => 'date',
    ],
    'filter' => [
      'id' => 'date',
    ],
    'sort' => [
      'id' => 'date',
    ],
  ];

  $data['audit_log']['hostname'] = [
    'title' => t('Hostname'),
    'help' => t('The IP address of the user triggering the event.'),
    'field' => [
      'id' => 'standard',
    ],
    'argument' => [
      'id' => 'string',
    ],
    'filter' => [
      'id' => 'string',
    ],
    'sort' => [
      'id' => 'standard',
    ],
  ];

  $data['audit_log']['location'] = [
    'title' => t('Location'),
    'help' => t('The URL origin of the event.'),
    'field' => [
      'id' => 'standard',
    ],
    'argument' => [
      'id' => 'string',
    ],
    'filter' => [
      'id' => 'string',
    ],
    'sort' => [
      'id' => 'standard',
    ],
  ];

  return $data;
}
