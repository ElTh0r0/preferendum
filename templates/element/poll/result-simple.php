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

<tr class="schedule-results valign-middle">
    <td>
        <div class="r r-legend r-yes"><?php echo __('Yes') . ':' ?></div>
    </td>
    <?php
    $numChoices = count($pollchoices);
    for ($i = 0; $i < $numChoices; $i++) {
        $yes = 0;
        foreach ($pollentries as $ent) {
            if ($ent[$pollchoices[$i]->id] == 1) {
                $yes++;
            }
        }
        echo '<td class="results-cell">';
        echo '<div class="r r-yes">' . $yes . '</div>';
        echo '</td>';
    }
    ?>
    <td class="schedule-blank"></td>
</tr>
