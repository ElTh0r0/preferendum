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
echo '<h1>' . __('Update user') . ' "' . $editUserName . '"</h1>';

if (strcmp($currentUserRole, $allroles[0]) != 0) {
    // If user us not admin, unset/hide ID
    $editUserId = null;
}

if (Configure::read('preferendum.sendBackendUserPwReset') || strcmp($currentUserRole, $allroles[0]) == 0) {
    echo $this->Form->create(
        $user,
        [
            'type' => 'post',
            'url' => ['action' => 'updateUser', $editUserId],
        ],
    );

    echo '<fieldset>';
    if (strcmp($currentUserRole, $allroles[0]) == 0) {
        echo $this->Form->control(
            'name',
            [
                'required' => true,
                'label' => __('Name'),
                'value' => $editUserName,
                'autocomplete' => 'off',
            ],
        );
    }

    if (Configure::read('preferendum.sendBackendUserPwReset')) {
        echo $this->Form->control(
            'email',
            [
                'label' => __('Email'),
                'value' => $editEmail,
                'autocomplete' => 'off',
            ],
        );
    }

    if (strcmp($currentUserRole, $allroles[0]) == 0) {
        echo $this->Form->label('selectrole', __('Role'));
        echo $this->Form->select(
            'role',
            $allroles,
            ['value' => array_search($editUserRole, $allroles), 'empty' => false, 'id' => 'selectrole'],
        );
    }
    echo '</fieldset><br>';
    echo $this->Form->button(__('Update user'));
    echo $this->Form->end();
    echo '<br>';
}
?>

<?php echo $this->Form->create(
    $user,
    [
        'type' => 'post',
        'url' => ['action' => 'updatePassword', $editUserId],
    ],
);
?>
<fieldset>
    <?php
    echo $this->Form->control(
        'password',
        [
            'required' => true,
            'label' => __('Password'),
            'type' => 'password',
            'autofocus' => true,
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
<?php echo $this->Form->button(__('Change password')); ?>
<?php echo $this->Form->end() ?>
