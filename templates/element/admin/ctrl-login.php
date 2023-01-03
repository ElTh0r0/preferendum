<?php
/**
 * PREFERendum (https://github.com/ElTh0r0/preferendum)
 * Copyright (c) github.com/ElTh0r0
 *
 * Licensed under The MIT License
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright 2020-2023 github.com/ElTh0r0
 * @license   MIT License (https://opensource.org/licenses/mit-license.php)
 * @link      https://github.com/ElTh0r0/preferendum
 * @version   0.5.0
 */
?>

<div>
    <?php echo $this->Flash->render() ?>
    <?php echo $this->Form->create() ?>
    <fieldset>
        <?php echo $this->Form->control(
            'name', [
            'required' => true,
            'label' => __('Name')]
        ) ?>
        <?php echo $this->Form->control(
            'password', [
            'required' => true,
            'label' => __('Password'),
            'type' => 'password']
        ) ?>
    </fieldset>
    <?php echo $this->Form->submit(__('Login')); ?>
    <?php echo $this->Form->end() ?>
</div>
