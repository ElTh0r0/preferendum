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

echo __('New comment in poll "{0}"', h($title)) . "\r\n\r\n";
echo __('From:') . ' ' . h($comment->name) . "\r\n";
echo __('Text:') . "\r\n" . h($comment->text) . "\r\n";
echo "\r\n";
echo __('Link:') . ' ' . $link . "\r\n";
echo "\r\n";
