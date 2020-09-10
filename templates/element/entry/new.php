<?php
/**
 * Sprudel-ng (https://github.com/ElTh0r0/sprudel-ng)
 * Copyright (c) github.com/ElTh0r0, github.com/bkis
 *
 * Licensed under The MIT License
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright 2019-2020 github.com/ElTh0r0, github.com/bkis
 * @license   MIT License (https://opensource.org/licenses/mit-license.php)
 * @link      https://github.com/ElTh0r0/sprudel-ng
 * @since     0.1.0
 */
?>

<?php
    echo $this->Form->create(
        $entry, [
        'type' => 'post',
        'url' => ['controller' => 'Entries', 'action' => 'new', $poll->pollid]
        ]
    );
    echo $this->Form->hidden(
        'pollid', [
        'value' => $poll->pollid,
        ]
    );
    echo '<td class="schedule-name-input">';
    echo $this->Form->text(
        'name', [
        'id' => 'name-input',
        'required' => 'true',
        'maxlength' => '32',
        'placeholder' => __('Your name?'),
        ]
    );

    echo '</td>';

    foreach ($poll->choices as $opt) {
        echo '<td class="new-entry-box new-entry-choice new-entry-choice-maybe">';
        echo $this->Form->hidden(
            'va', [
            'name' => 'values[]',
            'value' => '2',
            'class' => 'entry-value',
            ]
        );
        echo $this->Form->hidden(
            'op', [
            'name' => 'choices[]',
            'value' => $opt->option,
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
            'userdetails', [
            'id' => 'info-input',
            'maxlength' => '50',
            'placeholder' => __('Optional: Contact info'),
            ]
        );
        echo '</td></tr>';
    }
    echo $this->Form->end();
    ?>
