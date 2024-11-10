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

use Cake\Core\Configure;
?>

<div>
    <?php
    $base = $this->request->getUri()->getPath();
    $base = basename($base);
    if (strcmp($base, 'admin') == 0) {
        if (strcmp($currentUserRole, $adminRole) != 0) {
            echo $this->Html->link(
                $this->Form->button(
                    __('Update user'),
                    [
                        'type' => 'button',
                        'id' => 'ctrl-usermanagement',
                    ]
                ),
                ['controller' => 'Users', 'action' => 'edit'],
                ['escape' => false]
            );
        } else {
            echo $this->Html->link(
                $this->Form->button(
                    __('User management'),
                    [
                        'type' => 'button',
                        'id' => 'ctrl-usermanagement',
                    ]
                ),
                ['controller' => 'Users', 'action' => 'management'],
                ['escape' => false]
            );
        }
    } else {
        echo $this->Html->link(
            $this->Form->button(
                __('Poll administration'),
                [
                    'type' => 'button',
                    'id' => 'ctrl-polls',
                ]
            ),
            ['controller' => 'Admin', 'action' => 'index'],
            ['escape' => false]
        );
    }
    echo $this->Form->postLink(
        $this->Form->button(
            __('Logout'),
            [
                'type' => 'button',
                'id' => 'ctrl-logout',
            ]
        ),
        ['controller' => 'Admin', 'action' => 'logout'],
        ['escape' => false]
    ); ?>

    <?php if (Configure::read('preferendum.toggleTheme')) { ?>
        <button type="button" class="themeToggle" data-theme-toggle>&nbsp;</button>
    <?php } ?>
</div>
