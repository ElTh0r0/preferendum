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

<!-- TABLE HEADER / DATES -->
<tr>
    <td class="schedule-blank"></td>
    <?php foreach ($pollchoices as $choice) : ?>
        <td class="schedule-header" title="
        <?php
        echo h($choice->option);
        if ($poll->limitentry && $choice->max_entries > 0) {
            echo __(' - {0} pers.', $choice->max_entries);
        }
        ?>">
            <div>
                <div>
                    <?php echo h($choice->option) ?>
                    <?php
                    if ($poll->limitentry && $choice->max_entries > 0) {
                        echo __(' - {0} pers.', $choice->max_entries);
                    }
                    ?>
                </div>
            </div>
        </td>
    <?php endforeach; ?>
    <td class="schedule-blank"></td>
</tr>
