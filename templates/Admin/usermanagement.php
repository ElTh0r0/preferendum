<?php
/**
 * PREFERendum (https://github.com/ElTh0r0/preferendum)
 * Copyright (c) github.com/ElTh0r0
 *
 * Licensed under The MIT License
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright 2020-2022 github.com/ElTh0r0
 * @license   MIT License (https://opensource.org/licenses/mit-license.php)
 * @link      https://github.com/ElTh0r0/preferendum
 * @version   0.4.0
 */
?>

<!-- POLL CONTROLS -->
<div id="poll-controls">
    <div>
        <?php echo $this->element('admin/ctrl-logout'); ?>
    </div>
</div>

<div style="padding: 2rem;">
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
            echo '<tr><th><em>Name</em></th><th><em>&nbsp;&nbsp;Role</em></th><th></th></tr>';
            foreach ($backendusers as $backuser) {
                echo '<tr>';
                echo '<td>' . $backuser['name'] . '</td>';
                echo '<td>&nbsp;&nbsp;' . $backuser['role'] . '</td>';
                if ($cntAdmins > 1 || strcmp($backuser['role'], $allroles[0]) != 0) {
                    echo '<td>&nbsp;&nbsp;<span style="font-size: 0.8em;">';
                    echo $this->Form->postLink(
                        __('Delete'),
                        ['action' => 'deleteBackendUser', $backuser['id']],
                        ['escape' => false, 'confirm' => __('Are you sure to delete this user?')]
                    );
                    echo '</span></td>';
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
    
    <?php echo $this->Form->create($newOrUpdateUser) ?>
    <fieldset>
        <?php
        if (strcmp($currentUserRole, $allroles[0]) == 0) {
            echo $this->Form->control(
                'name', [
                'required' => true,
                'label' => __('Name')]
            );

            echo $this->Form->label('role', __('Role'));
            echo $this->Form->select('role', $allroles, ['val' => $allroles[0]]);
        } else {
            echo $this->Form->hidden('name', ['value' => $currentUserName]);
            echo $this->Form->hidden('role', ['value' => array_search($currentUserRole, $allroles)]);
        }
        ?>
        <?php echo $this->Form->hidden('poll_id', ['value' => '9999']) ?>
        <?php echo $this->Form->control(
            'password', [
            'required' => true,
            'label' => __('Password'),
            'type' => 'password']
        ) ?>
        <?php echo $this->Form->control(
            'confirmpassword', [
            'required' => true,
            'label' => __('Confirm password'),
            'type' => 'password']
        ) ?>
    </fieldset>
    <?php echo $this->Form->button(__('Submit')); ?>
    <?php echo $this->Form->end() ?>

</div>
