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

<tr class="schedule-results valign-middle">
    <td>
        <div class="r r-legend r-yes"><?php echo __('Yes') . ':' ?></div>
        <div class="r r-legend r-maybe"><?php echo __('Maybe') . ':' ?></div>
        <div class="r r-legend r-no"><?php echo __('No') . ':' ?></div>
        <div class="r r-legend"></div>
    </td>
<?php
    $maxTotal = 0;
    $entriesCount = sizeof($pollentries);

    $displayDates = array();
    $count = 0;
foreach ($poll->choices as $date) {
    $displayDates[$count++] = array(
        'date' => $date->option,
        'yes' => 0,
        'maybe' => 0,
        'no' => 0,
        'total' => 0
    );
}
    
for ($i = 0; $i < sizeof($poll->choices); $i++) {
    foreach ($pollentries as $ent) {
        if ($ent[$poll->choices[$i]->option] == 1) {
            $displayDates[$i]['yes']++;
            $displayDates[$i]['total'] += 2;
        } elseif ($ent[$poll->choices[$i]->option] == 2) {
            $displayDates[$i]['maybe']++;
            $displayDates[$i]['total'] += 1;
        } else {
            $displayDates[$i]['no']++;
        }
    }
}

if ($entriesCount > 0) {
    foreach ($displayDates as $date) {
        $maxTotal = max($date['total'] / ($entriesCount*2), $maxTotal);
    }
}
    
foreach ($displayDates as $date) {
    $date['score'] = $entriesCount > 0 ? ($date['total'] / ($entriesCount*2)) : 0;
    $date['score'] = $maxTotal > 0 ? ($date['score'] / $maxTotal) : 0;
    $dateDynStyles = 'opacity: ' . $date['score']  . '; ';
    $dateDynStyles .= 'background-size: ' . (($date['score'] * 100) - 10)  . '%; ';
    $dateDynStyles .= $date['score'] == 1 ? "background-image: url('" . $this->request->getAttributes()['webroot'] . "img/icon-heart.png');" : '';
    ?>
    <td class="results-cell">
        <div class="r r-yes"><?php echo $date['yes'] ?></div>
        <div class="r r-maybe"><?php echo $date['maybe'] ?></div>
        <div class="r r-no"><?php echo $date['no'] ?></div>
        <!-- date/option score visualization -->
        <div
            class="r r-total"
            style="<?php echo $dateDynStyles ?>">
        </div>
    </td>
<?php }    ?>
</tr>
