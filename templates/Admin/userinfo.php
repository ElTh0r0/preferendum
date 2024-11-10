<?php

/**
 * PREFERendum (https://github.com/ElTh0r0/preferendum)
 * Copyright (c) github.com/ElTh0r0, github.com/bkis
 *
 * Licensed under The MIT License
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright 2019-present github.com/ElTh0r0, github.com/bkis
 * @license   MIT License (https://opensource.org/licenses/mit-license.php)
 * @link      https://github.com/ElTh0r0/preferendum
 * @version   0.8.0
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
