<?php
/**
 * PREFERendum (https://github.com/ElTh0r0/preferendum)
 * Copyright (c) github.com/ElTh0r0, github.com/bkis
 *
 * Licensed under The MIT License
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright 2019-2023 github.com/ElTh0r0, github.com/bkis
 * @license   MIT License (https://opensource.org/licenses/mit-license.php)
 * @link      https://github.com/ElTh0r0/preferendum
 * @version   0.5.0
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
        'adminInterface' => false,
        // Turn on(true)/off(false) admin link functionality (recommended)
        'adminLinks' => true,

        // Add (optional) field to make result only visible for admin
        // (This feature can only be used, if 'adminLinks' is enabled and used, too.)
        'hidePollResult' => false,
        // Add (optional) field to store user contact information together with entry. Only admin can see the user info.
        // (This feature can only be used, if 'adminInterface' is enabled, too.)
        'collectUserinfo' => false,

        // Send an email after new poll entry or comment
        // After enabling this feature, it can be dis-/enabled for each poll separately and receiver can be defined.
        // Remark: "EmailTransport" has to be configured in app_local.php to be able to use it!
        'sendEntryEmail' => false,
        'sendCommentEmail' => false,

        // Header Logo (set to true if you want to show header logo, false otherwise)
        'headerLogo' => true,
        // Footer Text and Link
        // (Insert false if you want to remove the GitHub footer link.
        // Hot tipp of the week: Consider being nice and leaving it there!)
        'footerLink' => true,

        // Show trend visualization(true) or simple result just counting 'yes'(false)
        'trendResult' => true,

        // Maximum number of options / dates per poll
        'maxPollOptions' => 30,
        // Datepicker date format (e.g. 'yyyy-mm-dd' or 'dd.mm.yyyy')
        'datepickerFormat' => 'yyyy-mm-dd',

        // Date format for viewing comments (e.g. 'Y-m-d h:i a' or 'd.m.Y H:i')
        // See: https://www.php.net/manual/en/datetime.format.php
        'dateformatComments' => 'Y-m-d h:i a',

        // Lifespan (in days) of inactive polls (0 = disabled at all)
        // (read README.md for further instructions - cronjob needed!)
        'deleteInactivePollsAfter' => 0,
    ],
];
