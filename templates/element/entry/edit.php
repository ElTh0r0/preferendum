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
$edituser = '';
$editinfo = '';
if (in_array($userpw, $usermap_pw)) {
    $edituser = array_search($userpw, $usermap_pw);
    $editinfo = $usermap_info[$edituser];
}

if (
    !isset($adminid) ||
    (!strcmp($poll->adminid, $adminid) == 0)
) {
    $adminid = null;
}

echo $this->Form->create(
    $newentry,
    [
        'type' => 'post',
        'url' => ['controller' => 'Entries', 'action' => 'edit', $poll->id, $usermap[$edituser], $userpw, $adminid],
    ]
);

if ($poll->anonymous) {
    $editrow = array_search($userpw, array_values($usermap_pw)) + 1;
    echo '<td class="schedule-names">' . __('Edit entry') . ' #' . $editrow . '</td>';
} else {
    echo '<td class="schedule-name-input">';
    echo $this->Form->text(
        'name',
        [
            'id' => 'name-input',
            'required' => 'true',
            'maxlength' => '32',
            'placeholder' => __('Your name?'),
            'default' => $edituser,
            'autocomplete' => 'off',
        ]
    );
    echo '</td>';
}

$availableYes = [];
$numChoices = count($pollchoices);
if ($poll->limitentry) {
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

for ($i = 0; $i < $numChoices; $i++) {
    $entry = $pollchoices[$i]->id;
    $val = $pollentries[$edituser][$pollchoices[$i]->id];

    $txtvalue = 'maybe';
    $tdtitle = __('Maybe');
    switch ($val) {
        case 0:
            $txtvalue = 'no';
            $tdtitle = __('No');
            break;
        case 1:
            $txtvalue = 'yes';
            $tdtitle = __('Yes');
            break;
        case 2:
            $txtvalue = 'maybe';
            $tdtitle = __('Maybe');
            break;
    }

    $clickableOrFull = 'new-entry-box new-entry-choice new-entry-choice-' . $txtvalue;
    if ($poll->limitentry && $pollchoices[$i]->max_entries > 0) {
        if ($availableYes[$i] >= $pollchoices[$i]->max_entries && $val == 0) {
            $clickableOrFull = 'new-entry-choice-full';
            $tdtitle = __('Max. reached!');
        }
    }
    echo '<td class="' . $clickableOrFull . '" title="' . $tdtitle . '">';
    echo $this->Form->hidden(
        'va',
        [
            'name' => 'values[]',
            'value' => $val,
            'class' => 'entry-value',
        ]
    );
    echo $this->Form->hidden(
        'op',
        [
            'name' => 'choices[]',
            'value' => $entry,
            'class' => 'entry-date',
        ]
    );
    echo '</td>';
}

echo '<td class="schedule-submit">';
echo $this->Form->button(__('Save'));
echo '</td>';

if ($poll->userinfo == 1) {
    echo '<tr><td class="schedule-name-input">';
    echo $this->Form->text(
        'userdetails',
        [
            'id' => 'info-input',
            'maxlength' => '50',
            'placeholder' => __('Optional: Contact info'),
            'default' => $editinfo,
        ]
    );
    echo '</td></tr>';
}
echo $this->Form->end();
