<?php

/**
 * PREFERendum (https://github.com/ElTh0r0/preferendum)
 * Copyright (c) github.com/ElTh0r0, github.com/bkis
 *
 * Licensed under The MIT License
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright 2019-present github.com/ElTh0r0, github.com/bkis
 * @license   MIT License (https://opensource.org/licenses/mit-license.php)
 * @link      https://github.com/ElTh0r0/preferendum
 * @version   0.7.0
 */

/*
 * Configure PREFERendum and dis-/enable features.
 */

return [
    'App' => [
        // Available languages: 'en_US', 'de_DE'
        'defaultLocale' => 'en_US',
        // Timezone (e.g. 'UTC', 'Europe/Berlin', ...)
        'defaultTimezone' => 'UTC',
    ],

    'preferendum' => [
        // Turn on(true)/off(false) admin interface
        // See wiki: https://github.com/ElTh0r0/preferendum/wiki/5-Admin-interface
        'adminInterface' => false,

        // Turn on(true)/off(false) that backend users can request password reset by email
        // (This feature can only be used, if 'adminInterface' is enabled, too.)
        // Remark: "EmailTransport" has to be configured in app_local.php to be able to use it!
        // See wiki: https://github.com/ElTh0r0/preferendum/wiki/3.1-Email-setup
        'sendBackendUserPwReset' => false,

        // Turn on(true)/off(false) that only 'admin' or 'polladmin' users can create polls
        // (This feature can only be used, if 'adminInterface' is enabled, too.)
        'restrictPollCreation' => false,

        // Turn on(true)/off(false) to use admin links for ALL new polls automatically.
        'alwaysUseAdminLinks' => true,
        // If 'alwaysUseAdminLinks' is disabled, turn on(true)/off(false) admin link functionality optionally for each poll.
        'opt_AdminLinks' => false,

        // Turn on(true)/off(false) that users can change their entry through a personal link
        // (This feature can only be used, if 'alwaysUseAdminLinks' OR 'opt_AdminLinks' is enabled and used, too.)
        'opt_AllowChangeEntry' => true,

        // Turn on(true)/off(false) that users can send themselves their edit link by email
        // (This feature can only be used, if 'opt_AllowChangeEntry' is used.)
        // Remark: "EmailTransport" has to be configured in app_local.php to be able to use it!
        // See wiki: https://github.com/ElTh0r0/preferendum/wiki/3.1-Email-setup
        'opt_SendChangeEntryLink' => false,

        // Turn on(true)/off(false) to allow comments for ALL polls.
        'alwaysAllowComments' => true,
        // If 'alwaysAllowComments' is disabled, turn on(true)/off(false) to allow comments optionally for each poll.
        'opt_Comments' => false,

        // Add (optional) field to make votes only visible for admin
        // (This feature can only be used, if 'alwaysUseAdminLinks' OR 'opt_AdminLinks' is enabled and used, too.)
        'opt_HidePollVotes' => false,

        // Add (optional) field to store user contact information together with entry. Only admin can see the user info.
        // (This feature can only be used, if 'adminInterface' is enabled and if admin link is used for the poll)
        'opt_CollectUserinfo' => false,

        // Add (optional) field to make a poll with anonymous votes (no user name stored/shown)
        'opt_AnonymousVotes' => false,

        // Add (optional) field to protect access to poll with a password
        'opt_PollPassword' => false,

        // Send an email after new poll entry/comment or email with poll links after poll creation
        // After enabling this feature, it can be dis-/enabled for each poll separately and receiver can be defined.
        // Remark: "EmailTransport" has to be configured in app_local.php to be able to use it!
        // See wiki: https://github.com/ElTh0r0/preferendum/wiki/3.1-Email-setup
        'opt_SendEntryEmail' => false,
        'opt_SendCommentEmail' => false,
        'opt_SendPollCreationEmail' => false,

        // Optionally lock poll automatically at a certain date (0 = disabled at all)
        // Defined value will be used as default offset from today during poll creation, but date can be freely chosen by creator
        'opt_PollExpirationAfter' => 0,

        // Optionally define maximum number of entries per option
        'opt_MaxEntriesPerOption' => false,

        // Show vote result visualization. Current options:
        // 'trend': Showing kind of trend visualization weighting 'yes' and 'maybe'
        // 'simple': Just showing sum of 'yes' votes
        // 'none': No result visualization
        'resultVisualization' => 'trend',

        // Turn on(true)/off(false) that admin or polladmin can export & download the vote results as CSV
        'exportCsv' => false,

        // Maximum number of options / dates per poll
        'maxPollOptions' => 30,

        // Datepicker date format (e.g. 'yyyy-mm-dd' or 'dd.mm.yyyy')
        'datepickerFormat' => 'yyyy-mm-dd',

        // Date format for viewing comments (e.g. 'Y-m-d h:i a' or 'd.m.Y H:i')
        // See: https://www.php.net/manual/en/datetime.format.php
        'dateformatComments' => 'Y-m-d h:i a',

        // Lifespan (in days) of expired polls (0 = disabled at all)
        // See wiki for further instructions if cronjob shall be used:
        // https://github.com/ElTh0r0/preferendum/wiki/3.2-Cronjob
        'deleteExpiredPollsAfter' => 0,

        // Lifespan (in days) of inactive polls (0 = disabled at all)
        // See wiki for further instructions if cronjob shall be used:
        // https://github.com/ElTh0r0/preferendum/wiki/3.2-Cronjob
        'deleteInactivePollsAfter' => 0,

        // Enable switching light/dark theme
        'toggleTheme' => true,

        // Header Logo (set to true if you want to show header logo, false otherwise)
        'headerLogo' => true,
        // Footer Text and Link
        // (Insert false if you want to remove the GitHub footer link.
        // Hot tipp of the week: Consider being nice and leaving it there!)
        'footerLink' => true,
    ],
];
