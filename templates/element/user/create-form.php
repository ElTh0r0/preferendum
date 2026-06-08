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

<?php
echo '<h1>' . __('Create user') . '</h1>';

echo $this->Form->create(
    $user,
    [
        'type' => 'post',
        'url' => ['action' => 'add'],
    ],
);
?>
<fieldset>
    <?php
    echo $this->Form->control(
        'name',
        [
            'required' => true,
            'label' => __('Name'),
            'autocomplete' => 'off',
            'autofocus' => true,
        ],
    );

    if (Configure::read('preferendum.sendBackendUserPwReset')) {
        echo $this->Form->control(
            'email',
            [
                'label' => __('Email'),
                'autocomplete' => 'off',
            ],
        );
    }

    echo $this->Form->label('selectrole', __('Role'));
    echo $this->Form->select('role', $allroles, ['value' => 0, 'empty' => false, 'id' => 'selectrole']);

    echo $this->Form->control(
        'password',
        [
            'required' => true,
            'label' => __('Password'),
            'type' => 'password',
        ],
    );
    echo $this->Form->control(
        'confirmpassword',
        [
            'required' => true,
            'label' => __('Confirm password'),
            'type' => 'password',
        ],
    );
    ?>
</fieldset>
<?php echo $this->Form->button(__('Create')); ?>
<?php echo $this->Form->end() ?>
