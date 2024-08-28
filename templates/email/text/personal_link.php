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

echo __('Hello {0},', h($name)) . "\r\n\r\n";
echo __('You have made an entry in poll "{0}"', h($pollname)) . "\r\n";
echo __('You can edit your entry using the following link:') . ' ' . $link . "\r\n";
echo "\r\n";
