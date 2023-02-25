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
foreach ($pollentries as $name => $entry) {
    echo '<tr class="valign-middle">';
    echo '<td class="schedule-names">' . h($name) . '</td>';

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
?>
