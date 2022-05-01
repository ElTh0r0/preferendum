<?php
/**
 * PREFERendum (https://github.com/ElTh0r0/preferendum)
 * Copyright (c) github.com/ElTh0r0
 *
 * Licensed under The MIT License
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright 2020-2022 github.com/ElTh0r0
 * @license   MIT License (https://opensource.org/licenses/mit-license.php)
 * @link      https://github.com/ElTh0r0/preferendum
 * @version   0.4.0
 */
?>

<tr class="schedule-results valign-middle">
    <td>
        <div class="r r-legend r-yes"><?php echo __('Yes') . ':' ?></div>
        <!--<div class="r r-legend r-maybe">--><?php //echo __('Maybe') . ':' ?><!--</div>-->
        <!--<div class="r r-legend r-no">--><?php //echo __('No') . ':' ?><!--</div>-->
    </td>
<?php
for ($i = 0; $i < sizeof($poll->choices); $i++) {
    $no = 0;
    $yes = 0;
    $maybe = 0;
    foreach ($pollentries as $ent) {
        switch ($ent[$poll->choices[$i]->option]) {
        case 0: $no++; 
            break;
        case 1: $yes++; 
            break;
        case 2: $maybe++; 
            break;
        }
    }
    echo '<td class="results-cell">';
    echo '<div class="r r-yes">' . $yes . '</div>';
    // echo '<div class="r r-maybe">' . $maybe . '</div>';
    // echo '<div class="r r-no">' . $no . '</div>';
    echo '</td>';
}
?>
</tr>
