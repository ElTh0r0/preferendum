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

    $displayChoices = [];
    $count = 0;
    foreach ($pollchoices as $choice) {
        $displayChoices[$count++] = [
            'choice' => $choice->id,
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
                $displayChoices[$i]['yes']++;
                $displayChoices[$i]['total'] += 2;
            } elseif ($ent[$pollchoices[$i]->id] == 2) {
                $displayChoices[$i]['maybe']++;
                $displayChoices[$i]['total'] += 1;
            } else {
                $displayChoices[$i]['no']++;
            }
        }
    }

    if ($entriesCount > 0) {
        foreach ($displayChoices as $choice) {
            $maxTotal = max($choice['total'] / ($entriesCount * 2), $maxTotal);
        }
    }

    foreach ($displayChoices as $choice) {
        $choice['score'] = $entriesCount > 0 ? $choice['total'] / ($entriesCount * 2) : 0;
        $choice['score'] = $maxTotal > 0 ? $choice['score'] / $maxTotal : 0;
        $choiceDynStyles = 'opacity: ' . $choice['score'] . '; ';
        $size = ($choice['score'] * 100) - 10;
        $size = $size < 0 ? 0 : $size;
        $choiceDynStyles .= 'background-size: ' . $size . '%; ';
        $choiceDynStyles .= $choice['score'] == 1 ? "background-image: url('" .
            $this->request->getAttributes()['webroot'] . "img/icon-heart.png');" : '';
    ?>
        <td class="results-cell">
            <div class="r r-yes"><?php echo $choice['yes'] ?></div>
            <div class="r r-maybe"><?php echo $choice['maybe'] ?></div>
            <div class="r r-no"><?php echo $choice['no'] ?></div>
            <!-- option score visualization -->
            <div class="r r-total" style="<?php echo $choiceDynStyles ?>">
            </div>
        </td>
    <?php } ?>
    <td class="schedule-blank"></td>
</tr>