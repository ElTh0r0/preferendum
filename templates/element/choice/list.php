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
 * @version   0.7.0
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
</tr>
