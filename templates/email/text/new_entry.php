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
 * @version   0.8.0
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
