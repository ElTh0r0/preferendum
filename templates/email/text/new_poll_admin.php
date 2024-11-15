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

echo __('New poll "{0}" created', h($title)) . "\r\n\r\n";
echo __('This message should NOT be sent to the poll users. It contains your administration link!') . "\r\n";
echo "\r\n";
echo __('ADMIN link:') . ' ' . $link . "\r\n";
echo "\r\n";

if (!empty($password)) {
    echo __('Poll password:') . ' ' . $password . "\r\n";
    echo "\r\n";
}
