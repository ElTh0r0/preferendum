<?php
/**
 * PREFERendum (https://github.com/ElTh0r0/preferendum)
 * Copyright (c) github.com/ElTh0r0, github.com/bkis
 *
 * Licensed under The MIT License
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright 2019-2023 github.com/ElTh0r0, github.com/bkis
 * @license   MIT License (https://opensource.org/licenses/mit-license.php)
 * @link      https://github.com/ElTh0r0/preferendum
 * @version   0.5.0
 */
?>

<!-- TABLE HEADER / DATES -->
<tr>
    <td class="schedule-blank"></td>
    <?php foreach ($poll->choices as $choice): ?>
        <td class="schedule-header">
            <div>
                <div>
                    <?php echo h($choice->option) ?>
                </div>
            </div>
        </td>
    <?php endforeach; ?>
</tr>

<!-- EXISTING ENTRIES -->
<?php
if ($poll->hideresult == 0) {
    foreach ($pollentries as $name => $entry) {
        echo '<tr class="valign-middle">';
        echo '<td class="schedule-names">' . h($name) . '</td>';

        for ($i = 0; $i < sizeof($poll->choices); $i++) {
            $value = 'maybe';
            switch ($entry[$poll->choices[$i]->option]) {
            case 0: $value = 'no'; 
                break;
            case 1: $value = 'yes'; 
                break;
            case 2: $value = 'maybe'; 
                break;
            }

            echo '<td class="schedule-entry schedule-entry-' . $value . '"></td>';
        }
            
        echo '</tr>';
    }
}
?>
