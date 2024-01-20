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
 * @version   0.6.0
 */
?>

<div>
    <?php
    $base = $this->request->getUri()->getPath();
    $base = basename($base);
    if (strcmp($base, 'admin') == 0) {
        $caption = __('User management');
        if (strcmp($currentUserRole, $adminRole) != 0) {
            $caption = __('Change password');
        }
        echo $this->Html->link(
            $this->Form->button(
                $caption,
                [
                    'type' => 'button',
                    'id' => 'ctrl-usermanagement',
                ]
            ),
            ['controller' => 'Users', 'action' => 'index'],
            ['escape' => false]
        );
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

    <?php if (\Cake\Core\Configure::read('preferendum.toggleTheme')) { ?>
        <button type="button" class="themeToggle" data-theme-toggle>&nbsp;</button>
    <?php } ?>
</div>
