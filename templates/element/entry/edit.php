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
 * @version   0.5.0
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
        'url' => ['controller' => 'Entries', 'action' => 'edit', $poll->id, $usermap[$edituser], $userpw, $adminid]
    ]
);
echo '<td class="schedule-name-input">';
echo $this->Form->text(
    'name',
    [
        'id' => 'name-input',
        'required' => 'true',
        'maxlength' => '32',
        'placeholder' => __('Your name?'),
        'default' => $edituser,
    ]
);

echo '</td>';

for ($i = 0; $i < sizeof($pollchoices); $i++) {
    $entry = $pollchoices[$i]->id;
    $val = $pollentries[$edituser][$pollchoices[$i]->id];

    $txtvalue = 'maybe';
    switch ($val) {
        case 0:
            $txtvalue = 'no';
            break;
        case 1:
            $txtvalue = 'yes';
            break;
        case 2:
            $txtvalue = 'maybe';
            break;
    }

    echo '<td class="new-entry-box new-entry-choice new-entry-choice-' . $txtvalue . '">';
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
?>
