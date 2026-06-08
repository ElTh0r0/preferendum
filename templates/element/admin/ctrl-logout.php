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

use Cake\Core\Configure;
?>

<div>
    <?php
    $base = $this->request->getUri()->getPath();
    $base = basename($base);
    if (strcmp($base, 'admin') == 0) {
        if (strcmp($currentUserRole, $adminRole) != 0) {
            echo $this->Html->link(
                __('Update user'),
                ['controller' => 'Users', 'action' => 'edit'],
                [
                    'class' => 'button',
                    'id' => 'ctrl-usermanagement',
                    'escape' => false,
                ],
            );
        } else {
            echo $this->Html->link(
                __('User management'),
                ['controller' => 'Users', 'action' => 'management'],
                [
                    'class' => 'button',
                    'id' => 'ctrl-usermanagement',
                    'escape' => false,
                ],
            );
        }
    } else {
        echo $this->Html->link(
            __('Poll administration'),
            ['controller' => 'Admin', 'action' => 'index'],
            [
                'class' => 'button',
                'id' => 'ctrl-polls',
                'escape' => false,
            ],
        );
    }
    echo $this->Form->postLink(
        __('Logout'),
        ['controller' => 'Admin', 'action' => 'logout'],
        [
            'class' => 'button',
            'id' => 'ctrl-logout',
            'escape' => false,
        ],
    ); ?>

    <?php if (Configure::read('preferendum.toggleTheme')) { ?>
        <button type="button" class="themeToggle" data-theme-toggle>&nbsp;</button>
    <?php } ?>
</div>
