<?php

/**
 * PREFERendum (https://github.com/ElTh0r0/preferendum)
 * Copyright (c) github.com/ElTh0r0
 *
 * Licensed under The MIT License
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright 2020-present github.com/ElTh0r0
 * @license   MIT License (https://opensource.org/licenses/mit-license.php)
 * @link      https://github.com/ElTh0r0/preferendum
 * @version   0.6.0
 */

echo __('A password reset was requested for your account!') . "\r\n\r\n";
echo __('Account name:') . ' ' . h($username) . "\r\n";
echo __('New password:') . ' ' . h($newpassword) . "\r\n\r\n";
echo __('Please change your password after the first login.') . "\r\n" . $loginurl;
echo "\r\n";
