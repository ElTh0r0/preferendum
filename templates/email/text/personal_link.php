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

echo __('Hello {0},', h($name)) . "\r\n\r\n";
echo __('You have made an entry in poll "{0}"', h($pollname)) . "\r\n";
echo __('You can edit your entry using the following link:') . ' ' . $link . "\r\n";
echo "\r\n";
