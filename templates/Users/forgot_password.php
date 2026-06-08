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

<div id="control-elements">
    <div>
        <?php if (Configure::read('preferendum.toggleTheme')) { ?>
            <button type="button" class="themeToggle" data-theme-toggle>&nbsp;</button>
        <?php } ?>
    </div>
</div>

<div id="useradmin-page">
    <?php
    echo $this->Flash->render();

    echo '<h1>' . __('Password reset') . '</h1>';
    echo '<p>' . __('Enter your email address and we\'ll send you a new password for your account:') . '<p>';

    echo $this->Form->create(
        null,
        [
            'type' => 'post',
            'url' => ['action' => 'forgotPassword'],
        ],
    );

    echo '<fieldset>';
    echo $this->Form->control(
        'email',
        [
            'required' => true,
            'label' => '',
            'value' => '',
            'autocomplete' => 'email',
        ],
    );
    echo '</fieldset><br>';

    echo $this->Form->button(__('Send new password'));
    echo $this->Form->end();

    echo '<br>';
    echo $this->Html->link(
        __('Back to login'),
        '/Admin/login',
    );
    ?>
</div>
