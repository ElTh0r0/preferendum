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

<!-- EXISTING ENTRIES -->
<?php
$link = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://$_SERVER[HTTP_HOST]" . $this->request->getAttributes()['webroot'] . 'polls/' . $poll->id . '/NA/';
$cnt = 0;
foreach ($pollentries as $name => $entry) {
    $cnt++;
    echo '<tr class="valign-middle">';
    if ($poll->anonymous) {
        echo '<td class="schedule-names">' . $cnt . '</td>';
    } else {
        echo '<td class="schedule-names">' . h($name) . '</td>';
    }

    for ($i = 0; $i < sizeof($pollchoices); $i++) {
        $value = 'maybe';
        switch ($entry[$pollchoices[$i]->id]) {
            case 0:
                $value = 'no';
                break;
            case 1:
                $value = 'yes';
                break;
            case 2:
                $value = 'maybe';
                break;
        }

        echo '<td class="schedule-entry schedule-entry-' . $value . '"></td>';
    }

    echo '<td>';
    if ($poll->editentry) {
        echo '<div style="position: absolute; left: -99999px;"><input type="text" id="entry-link-' . $cnt . '" value="' . $link . $usermap_pw[$name] . '" readonly /></div>';
        echo '<button type="button" class="copy-trigger entry-copy-link" data-clipboard-target="#entry-link-' . $cnt . '" title="' . __('Copy entry edit link to clipboard') . '"></button>';
        echo ' ';
    }
    echo $this->Form->postLink(
        $this->Form->button(
            '',
            [
                'type' => 'button', 'class' => 'entry-edit'
            ]
        ),
        ['controller' => 'Polls', 'action' => 'edit', $poll->id, $adminid, $usermap_pw[$name]],
        ['escape' => false]
    );
    echo ' ';
    echo $this->Form->postLink(
        $this->Form->button(
            '',
            [
                'type' => 'button', 'class' => 'schedule-delete'
            ]
        ),
        ['controller' => 'Users', 'action' => 'deleteUserAndPollEntries', $poll->id, $adminid, $usermap[$name]],
        ['escape' => false, 'confirm' => __('Are you sure to delete this entry?')]
    );
    echo '</td>';

    echo '</tr>';
}

if (in_array($userpw, $usermap_pw)) {
    echo '<tr class="schedule-new valign-middle">';
    echo $this->element('entry/edit');
    echo '</tr>';

    echo '<tr class="table-spacer-row table-spacer-row-big"><td></td></tr>';
}
?>
