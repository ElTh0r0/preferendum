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

echo __('New poll "{0}" created', h($title)) . "\r\n\r\n";
echo __('This email can be forwarded to any user who shall participated the poll!') . "\r\n";
echo "\r\n";
echo __('USER link:') . ' ' . $link . "\r\n";
echo "\r\n";

if (!empty($password)) {
    echo __('For accessing the poll, following password is required:') . ' ' . $password . "\r\n";
    echo "\r\n";
}
