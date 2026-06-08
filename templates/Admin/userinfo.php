<?php

/**
 * PREFERendum
 *
 * SPDX-FileCopyrightText: codeberg.org/ElTh0r0, github.com/bkis
 * SPDX-License-Identifier: MIT
 *
 * @copyright 2019-present codeberg.org/ElTh0r0, github.com/bkis
 * @license   MIT License (https://opensource.org/license/MIT)
 * @link      https://codeberg.org/ElTh0r0/preferendum
 */
?>
<?php $this->assign('title', __('User info') . ' - ' . $polltitle); ?>

<div id="admin-page">
    <?php
    echo '<table style="min-width: 500px">';
    echo '<tr><td colspan="2"><h1>' . __('User info for poll') . ' "' . $polltitle . '"</h1></td></tr>';
    if (count($userinfos) > 0) {
        echo '<tr><td><em>' . __('Name') . '</em></td><td><em>' . __('Info') . '</em></td></tr>';
        foreach ($userinfos as $uinfo) {
            echo '<tr><td>' . h($uinfo->name) . '</td><td>' . h($uinfo->info) . '</td></tr>';
        }
    } else {
        echo '<tr><td colspan="2" class="fail">' . __('No user information available for this poll!') . '</td></tr>';
    }

    echo '</table>';
    ?>
</div>
