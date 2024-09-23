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

<tr class="schedule-results valign-middle">
    <td>
        <div class="r r-legend r-yes"><?php echo __('Yes') . ':' ?></div>
        <div class="r r-legend r-maybe"><?php echo __('Maybe') . ':' ?></div>
        <div class="r r-legend r-no"><?php echo __('No') . ':' ?></div>
        <div class="r r-legend"></div>
    </td>
    <?php
    $maxTotal = 0;
    $entriesCount = count($pollentries);

    $displayDates = [];
    $count = 0;
    foreach ($pollchoices as $date) {
        $displayDates[$count++] = [
            'date' => $date->id,
            'yes' => 0,
            'maybe' => 0,
            'no' => 0,
            'total' => 0,
        ];
    }

    $numChoices = count($pollchoices);
    for ($i = 0; $i < $numChoices; $i++) {
        foreach ($pollentries as $ent) {
            if ($ent[$pollchoices[$i]->id] == 1) {
                $displayDates[$i]['yes']++;
                $displayDates[$i]['total'] += 2;
            } elseif ($ent[$pollchoices[$i]->id] == 2) {
                $displayDates[$i]['maybe']++;
                $displayDates[$i]['total'] += 1;
            } else {
                $displayDates[$i]['no']++;
            }
        }
    }

    if ($entriesCount > 0) {
        foreach ($displayDates as $date) {
            $maxTotal = max($date['total'] / ($entriesCount * 2), $maxTotal);
        }
    }

    foreach ($displayDates as $date) {
        $date['score'] = $entriesCount > 0 ? $date['total'] / ($entriesCount * 2) : 0;
        $date['score'] = $maxTotal > 0 ? $date['score'] / $maxTotal : 0;
        $dateDynStyles = 'opacity: ' . $date['score'] . '; ';
        $size = ($date['score'] * 100) - 10;
        $size = $size < 0 ? 0 : $size;
        $dateDynStyles .= 'background-size: ' . $size . '%; ';
        $dateDynStyles .= $date['score'] == 1 ? "background-image: url('" .
            $this->request->getAttributes()['webroot'] . "img/icon-heart.png');" : '';
    ?>
        <td class="results-cell">
            <div class="r r-yes"><?php echo $date['yes'] ?></div>
            <div class="r r-maybe"><?php echo $date['maybe'] ?></div>
            <div class="r r-no"><?php echo $date['no'] ?></div>
            <!-- date/option score visualization -->
            <div class="r r-total" style="<?php echo $dateDynStyles ?>">
            </div>
        </td>
    <?php } ?>
</tr>
