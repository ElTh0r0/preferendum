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
                __('Update user'),
                ['controller' => 'Users', 'action' => 'edit'],
                [
                    'class' => 'button',
                    'id' => 'ctrl-usermanagement',
                    'escape' => false,
                ]
            );
        } else {
            echo $this->Html->link(

                __('User management'),
                ['controller' => 'Users', 'action' => 'management'],
                [
                    'class' => 'button',
                    'id' => 'ctrl-usermanagement',
                    'escape' => false,
                ]
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
            ]
        );
    }
    echo $this->Form->postLink(
        __('Logout'),
        ['controller' => 'Admin', 'action' => 'logout'],
        [
            'class' => 'button',
            'id' => 'ctrl-logout',
            'escape' => false,
        ]
    ); ?>

    <?php if (Configure::read('preferendum.toggleTheme')) { ?>
        <button type="button" class="themeToggle" data-theme-toggle>&nbsp;</button>
    <?php } ?>
</div>
