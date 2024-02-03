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

<!-- POLL CONTROLS -->
<div id="poll-controls">
    <div>
        <?php echo $this->element('admin/ctrl-logout'); ?>
    </div>
</div>

<div id="useradmin-page">
    <?php echo $this->Flash->render() ?>
    <?php
    if (strcmp($currentUserRole, $allroles[0]) == 0) { ?>
        <h1><?php echo __('Available users') ?></h1>
        <table>
            <?php
            $cntAdmins = 0;
            foreach ($backendusers as $backuser) {
                if (strcmp($backuser['role'], $allroles[0]) == 0) {
                    $cntAdmins = $cntAdmins + 1;
                }
            }
            echo '<tr><td><em>Name</em></td><td><em>Role</em></td><td></td></tr>';
            foreach ($backendusers as $backuser) {
                echo '<tr>';
                echo '<td>' . $backuser['name'] . '</td>';
                echo '<td>' . $backuser['role'] . '</td>';
                if ($cntAdmins > 1 || strcmp($backuser['role'], $allroles[0]) != 0) {
                    echo '<td><span style="font-size: 0.8em;">';
                    echo $this->Form->postLink(
                        __('Delete'),
                        ['action' => 'deleteBackendUser', $backuser['id']],
                        ['escape' => false, 'confirm' => __('Are you sure to delete this user?')]
                    );
                    echo '</span></td>';
                } else {
                    echo '<td></td>';
                }
                echo '</tr>';
            }
            ?>
        </table>
        <br />
        <hr />
    <?php } ?>

    <?php
    if (strcmp($currentUserRole, $allroles[0]) == 0) {
        echo '<h1>' . __('Create user / change password') . '</h1>';
    } else {
        echo '<h1>' . __('Change password') . '</h1>';
    }
    ?>

    <?php
    echo $this->Form->create(
        $user,
        [
            'type' => 'post',
            'url' => ['controller' => 'Users', 'action' => 'addOrUpdateUser', $currentUserName, $currentUserRole]
        ]
    );
    ?>
    <fieldset>
        <?php
        if (strcmp($currentUserRole, $allroles[0]) == 0) {
            echo $this->Form->control(
                'name',
                [
                    'required' => true,
                    'label' => __('Name'),
                    'autocomplete' => 'off',
                ]
            );

            echo $this->Form->label('selectrole', __('Role'));
            echo $this->Form->select('role', $allroles, ['value' => 0, 'empty' => false, 'id' => 'selectrole']);
        }
        ?>
        <?php echo $this->Form->control(
            'password',
            [
                'required' => true,
                'label' => __('Password'),
                'type' => 'password'
            ]
        ) ?>
        <?php echo $this->Form->control(
            'confirmpassword',
            [
                'required' => true,
                'label' => __('Confirm password'),
                'type' => 'password'
            ]
        ) ?>
    </fieldset>
    <?php echo $this->Form->button(__('Submit')); ?>
    <?php echo $this->Form->end() ?>

</div>
