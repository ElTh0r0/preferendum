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
?>

<div id="control-elements">
    <div>
        <?php echo $this->element('admin/ctrl-logout'); ?>
    </div>
</div>

<div id="useradmin-page">
    <?php
    echo $this->Flash->render();

    if (strcmp($currentUserRole, $allroles[0]) == 0) {
        echo $this->element('user/list');
        echo '<br>';
        echo '<hr>';

        echo $this->Html->link(
            '&larr; ' . __('Back to user creation'),
            ['action' => 'management'],
            ['escape' => false],
        );
    }

    echo $this->element('user/edit-form');
    ?>
</div>
