<?php
/**
 * Sprudel-ng (https://github.com/ElTh0r0/sprudel-ng)
 * Copyright (c) github.com/ElTh0r0
 *
 * Licensed under The MIT License
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright 2020 github.com/ElTh0r0
 * @license   MIT License (https://opensource.org/licenses/mit-license.php)
 * @link      https://github.com/ElTh0r0/sprudel-ng
 * @since     0.1.0
 */

echo __('New entry in poll "{0}"', h($title)) . "\r\n\r\n";
echo __('From:') . ' ' . h($name) . "\r\n";
echo __('Options:') . "\r\n";
foreach ($entries as $option => $entry) {
    $val = '?';
    if ($entry == 0) {
        $val = __('No');
    } else if ($entry == 1) {
        $val = __('Yes');
    }
    echo ' - ' . h($option) . ': ' . $val . "\r\n";
}
echo "\r\n";
echo __('Link:') . ' ' . $link . "\r\n";
echo "\r\n";