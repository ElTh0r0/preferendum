<?php

/**
 * PREFERendum
 *
 * SPDX-FileCopyrightText: codeberg.org/ElTh0r0
 * SPDX-License-Identifier: MIT
 *
 * @copyright 2020-present codeberg.org/ElTh0r0
 * @license   MIT License (https://opensource.org/license/MIT)
 * @link      https://codeberg.org/ElTh0r0/preferendum
 */

echo __('A password reset was requested for your account!') . "\r\n\r\n";
echo __('Account name:') . ' ' . h($username) . "\r\n";
echo __('New password:') . ' ' . h($newpassword) . "\r\n\r\n";
echo __('Please change your password after the first login.') . "\r\n" . $loginurl;
echo "\r\n";
