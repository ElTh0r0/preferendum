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

<!-- EXISTING ENTRIES -->
<?php
$cnt = 0;
foreach ($pollentries as $name => $entry) {
    $cnt++;
    echo '<tr class="valign-middle">';
    if ($poll->anonymous) {
        echo '<td class="schedule-names">' . $cnt . '</td>';
    } else {
        echo '<td class="schedule-names">' . h($name) . '</td>';
    }

    $numChoices = count($pollchoices);
    for ($i = 0; $i < $numChoices; $i++) {
        $value = 'maybe';
        switch ($entry[$pollchoices[$i]->id]) {
            case 0:
                $value = 'no';
                break;
            case 1:
                $value = 'yes';
                break;
            case 2:
                $value = 'maybe';
                break;
        }

        echo '<td class="schedule-entry schedule-entry-' . $value . '"></td>';
    }

    echo '<td class="schedule-blank"></td>';
    echo '</tr>';
}
?>
