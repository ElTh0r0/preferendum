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

echo __('Entry in poll "{0}"', h($title)) . "\r\n\r\n";
echo __('From:') . ' ' . h($name) . "\r\n";
echo __('Options:') . "\r\n";

foreach ($entries as $entry) {
    $val = __('Maybe');
    if ($entry->value == 0) {
        $val = __('No');
    } elseif ($entry->value == 1) {
        $val = __('Yes');
    }
    echo ' - ' . h($entry->choice->option) . ': ' . $val . "\r\n";
}

echo "\r\n";
echo __('Link:') . ' ' . $link . "\r\n";
echo "\r\n";
