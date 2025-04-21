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

<?php

echo '<tr class="schedule-new valign-middle">';

if ($poll->anonymous) {
    echo '<td class="schedule-blank"></td>';
} else {
    echo '<td class="schedule-name-input">';
    echo $this->Form->text(
        'name',
        [
            'id' => 'name-input',
            'form' => 'entry_form',
            'required' => 'true',
            'maxlength' => '32',
            'placeholder' => __('Your name?'),
            'autocomplete' => 'off',
        ]
    );
    echo '</td>';
}

$availableYes = [];
if ($poll->limitentry) {
    $numChoices = count($pollchoices);
    for ($i = 0; $i < $numChoices; $i++) {
        $yes = 0;
        foreach ($pollentries as $ent) {
            if ($ent[$pollchoices[$i]->id] == 1 || $ent[$pollchoices[$i]->id] == 2) {
                $yes++;
            }
        }
        $availableYes[] = $yes;
    }
}

$j = 0;
foreach ($pollchoices as $opt) {
    $clickableOrFull = 'new-entry-box new-entry-choice new-entry-choice-no';
    $textNoFull = __('No');
    if ($poll->limitentry && $opt->max_entries > 0) {
        if ($availableYes[$j] >= $opt->max_entries) {
            $clickableOrFull = 'new-entry-choice-full';
            $textNoFull = __('Max. reached!');
        }
    }
    echo '<td class="' . $clickableOrFull . '" title="' . $textNoFull . '">';

    echo $this->Form->hidden(
        'va',
        [
            'name' => 'values[]',
            'form' => 'entry_form',
            'value' => '0',
            'class' => 'entry-value',
        ]
    );
    echo $this->Form->hidden(
        'op',
        [
            'name' => 'choices[]',
            'form' => 'entry_form',
            'value' => $opt->id,
            'class' => 'entry-date',
        ]
    );
    echo '</td>';
    $j++;
}
echo '<td class="schedule-submit">';
echo $this->Form->button(__('Save'), ['form' => 'entry_form']);
echo '</td>';
echo '</tr>';

if ($poll->userinfo == 1) {
    echo '<tr><td class="schedule-name-input">';
    echo $this->Form->text(
        'userdetails',
        [
            'id' => 'info-input',
            'form' => 'entry_form',
            'maxlength' => '50',
            'placeholder' => __('Optional: Contact info'),
        ]
    );
    echo '</td><td class="schedule-blank" colspan="' . (count($pollchoices) + 1) . '"></td></tr>';
}
