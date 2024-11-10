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
 * @version   0.8.0
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
</tr>
