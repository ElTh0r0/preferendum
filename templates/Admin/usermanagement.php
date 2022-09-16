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
    $extUser = \Cake\Core\Configure::read('preferendum.extendedUsermanagementAccess');
    if (in_array($currentUserId, $extUser)) { ?>
        <h1><?php echo __('Available users') ?></h1>
        <table>
            <?php
            foreach ($admins as $adm) {
                echo '<tr>';
                echo '<td>' . $adm['name'] . '</td>';
                echo '<td>&nbsp;&nbsp;<span style="font-size: 0.8em;">ID: ' . $adm['id'] . '</span></td>';
                if (sizeof($admins) > 1) {
                    echo '<td>&nbsp;&nbsp;<span style="font-size: 0.8em;">';
                    echo $this->Form->postLink(
                        __('Delete'),
                        ['action' => 'deleteAdmin', $adm['id']],
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
    if (in_array($currentUserId, $extUser)) {
        echo '<h1>' . __('Create user / change password') . '</h1>';
    } else {
        echo '<h1>' . __('Change password') . '</h1>';
    }
    ?>
    
    <?php echo $this->Form->create($user) ?>
    <fieldset>
        <?php
        if (in_array($currentUserId, $extUser)) {
            echo $this->Form->control(
                'name', [
                'required' => true,
                'label' => __('Name')]
            );
        } else {
            echo $this->Form->hidden('name', ['value' => $currentUserName]);    
        }
        ?>
        <?php echo $this->Form->hidden('poll_id', ['value' => $polladmid]) ?>
        <?php echo $this->Form->control(
            'info', [
            'required' => true,
            'label' => __('Password'),
            'type' => 'password']
        ) ?>
        <?php echo $this->Form->control(
            'confirminfo', [
            'required' => true,
            'label' => __('Confirm password'),
            'type' => 'password']
        ) ?>
    </fieldset>
    <?php echo $this->Form->button(__('Submit')); ?>
    <?php echo $this->Form->end() ?>

</div>
