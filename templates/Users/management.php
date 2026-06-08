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

    echo $this->element('user/list');
    echo '<br>';
    echo '<hr>';
    echo $this->element('user/create-form');
    ?>
</div>
