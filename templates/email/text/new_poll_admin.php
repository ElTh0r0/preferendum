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
echo __('This message should NOT be sent to the poll users. It contains your administration link!') . "\r\n";
echo "\r\n";
echo __('ADMIN link:') . ' ' . $link . "\r\n";
echo "\r\n";

if (!empty($password)) {
    echo __('Poll password:') . ' ' . $password . "\r\n";
    echo "\r\n";
}
