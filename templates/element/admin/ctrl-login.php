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
 * @version   0.7.1
 */
?>

<div>
    <?php echo $this->Flash->render() ?>
    <?php if (\Cake\Core\Configure::read('preferendum.toggleTheme')) { ?>
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
                'value' => isset($pollid) ? $pollid : '',
                'autocomplete' => 'username',
            ]
        );
        ?>
        <?php echo $this->Form->control(
            'password',
            [
                'required' => true,
                'label' => __('Password'),
                'type' => 'password',
                'autocomplete' => 'current-password',
            ]
        ); ?>
    </fieldset>
    <?php echo $this->Form->submit(__('Login')); ?>
    <?php echo $this->Form->end() ?>

    <?php if (\Cake\Core\Configure::read('preferendum.sendBackendUserPwReset')) {
        echo '<span style="font-size: 0.8em;">';
        echo $this->Html->link(
            __('Forgot password'),
            '/Users/forgotPassword',
        );
        echo '</span>';
    } ?>
</div>
