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
    <?php echo $this->Flash->render() ?>
    <?php if (Configure::read('preferendum.toggleTheme')) { ?>
        <button type="button" class="themeToggle" data-theme-toggle>&nbsp;</button>
    <?php } ?>
    <?php echo $this->Form->create() ?>
    <fieldset>
        <?php
        echo $this->Form->control(
            'name',
            [
                'required' => true,
                'label' => isset($pollid) ? '' : __('Name'),
                'hidden' => isset($pollid),
                'value' => $pollid ?? '', // same as: isset($pollid) ? $pollid : '',
                'autocomplete' => 'username',
                'autofocus' => !isset($pollid),
            ],
        );
        ?>
        <?php echo $this->Form->control(
            'password',
            [
                'required' => true,
                'label' => __('Password'),
                'type' => 'password',
                'autocomplete' => 'current-password',
                'autofocus' => isset($pollid),
            ],
        ); ?>
    </fieldset>
    <?php echo $this->Form->submit(__('Login')); ?>
    <?php echo $this->Form->end() ?>

    <?php if (Configure::read('preferendum.sendBackendUserPwReset')) {
        echo '<span style="font-size: 0.8em;">';
        echo $this->Html->link(
            __('Forgot password'),
            '/Users/forgotPassword',
        );
        echo '</span>';
    } ?>
</div>
