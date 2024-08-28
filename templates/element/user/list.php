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

<h1><?php echo __('Available users') ?></h1>
<table>
    <?php
    $cntAdmins = 0;
    foreach ($backendusers as $backuser) {
        if (strcmp($backuser['role'], $allroles[0]) == 0) {
            $cntAdmins = $cntAdmins + 1;
        }
    }
    echo '<tr><td><em>' . __('Name') . '</em></td><td><em>' . __('Role') . '</em></td>';
    if (\Cake\Core\Configure::read('preferendum.sendBackendUserPwReset')) {
        echo '<td><em>' . __('Email') . '</em></td>';
    }
    echo '<td></td><td></td></tr>';
    foreach ($backendusers as $backuser) {
        echo '<tr>';
        echo '<td>' . $backuser['name'] . '</td>';
        echo '<td>' . $backuser['role'] . '</td>';
        if (\Cake\Core\Configure::read('preferendum.sendBackendUserPwReset')) {
            echo '<td>' . $backuser['info'] . '</td>';
        }
        echo '<td><span style="font-size: 0.8em;">';
        echo $this->Form->postLink(
            __('Edit'),
            ['action' => 'edit', $backuser['id']]
        );
        echo '</span></td>';

        if ($cntAdmins > 1 || strcmp($backuser['role'], $allroles[0]) != 0) {
            echo '<td><span style="font-size: 0.8em;">';
            echo $this->Form->postLink(
                __('Delete'),
                ['action' => 'deleteBackendUser', $backuser['id']],
                ['escape' => false, 'confirm' => __('Are you sure to delete user {0}?', h($backuser['name']))]
            );
            echo '</span></td>';
        } else {
            echo '<td></td>';
        }
        echo '</tr>';
    }
    ?>
</table>
