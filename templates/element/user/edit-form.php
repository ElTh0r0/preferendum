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
 * @version   0.7.0
 */
?>

<?php
echo '<h1>' . __('Update user') . ' "' . $editUserName . '"</h1>';

if (strcmp($currentUserRole, $allroles[0]) != 0) {
    // If user us not admin, unset/hide ID
    $editUserId = null;
}

if (\Cake\Core\Configure::read('preferendum.sendBackendUserPwReset') || strcmp($currentUserRole, $allroles[0]) == 0) {
    echo $this->Form->create(
        $user,
        [
            'type' => 'post',
            'url' => ['action' => 'updateUser', $editUserId]
        ]
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
            ]
        );
    }

    if (\Cake\Core\Configure::read('preferendum.sendBackendUserPwReset')) {
        echo $this->Form->control(
            'email',
            [
                'label' => __('Email'),
                'value' => $editEmail,
                'autocomplete' => 'off',
            ]
        );
    }

    if (strcmp($currentUserRole, $allroles[0]) == 0) {
        echo $this->Form->label('selectrole', __('Role'));
        echo $this->Form->select('role', $allroles, ['value' => array_search($editUserRole, $allroles), 'empty' => false, 'id' => 'selectrole']);
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
        'url' => ['action' => 'updatePassword', $editUserId]
    ]
);
?>
<fieldset>
    <?php
    echo $this->Form->control(
        'password',
        [
            'required' => true,
            'label' => __('Password'),
            'type' => 'password'
        ]
    );
    echo $this->Form->control(
        'confirmpassword',
        [
            'required' => true,
            'label' => __('Confirm password'),
            'type' => 'password'
        ]
    );
    ?>
</fieldset>
<?php echo $this->Form->button(__('Change password')); ?>
<?php echo $this->Form->end() ?>
