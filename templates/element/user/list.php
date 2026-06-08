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
    if (Configure::read('preferendum.sendBackendUserPwReset')) {
        echo '<td><em>' . __('Email') . '</em></td>';
    }
    echo '<td></td><td></td></tr>';
    foreach ($backendusers as $backuser) {
        echo '<tr>';
        echo '<td>' . $backuser['name'] . '</td>';
        echo '<td>' . $backuser['role'] . '</td>';
        if (Configure::read('preferendum.sendBackendUserPwReset')) {
            echo '<td>' . $backuser['info'] . '</td>';
        }
        echo '<td>';
        echo $this->Form->postLink(
            __('Edit'),
            ['action' => 'edit', $backuser['id']],
            ['style' => 'font-size: 0.8em;'],
        );
        echo '</td>';

        if ($cntAdmins > 1 || strcmp($backuser['role'], $allroles[0]) != 0) {
            echo '<td>';
            echo $this->Form->postLink(
                __('Delete'),
                ['action' => 'deleteBackendUser', $backuser['id']],
                [
                    'escape' => false,
                    'confirm' => __('Are you sure to delete user {0}?', h($backuser['name'])),
                    'style' => 'font-size: 0.8em;',
                ],
            );
            echo '</td>';
        } else {
            echo '<td></td>';
        }
        echo '</tr>';
    }
    ?>
</table>
