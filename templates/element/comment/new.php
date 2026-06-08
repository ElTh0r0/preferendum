<?php

/**
 * PREFERendum
 *
 * SPDX-FileCopyrightText: codeberg.org/ElTh0r0
 * SPDX-License-Identifier: MIT
 *
 * @copyright 2020-present codeberg.org/ElTh0r0
 * @license   MIT License (https://opensource.org/license/MIT)
 * @link      https://codeberg.org/ElTh0r0/preferendum
 */
?>

<div class="center-box">
    <?php
    if ($poll->hidevotes == 1) {
        echo '<h1>' . __('Comments') . '</h1>';
        echo __('Comments will only be visible for the poll admin!') . '<br>&nbsp;';
    }

    echo $this->Form->create(
        $newcomment,
        [
            'type' => 'post',
            'url' => ['controller' => 'Comments', 'action' => 'add', $poll->id],
        ],
    );
    echo $this->Form->control(
        'name',
        [
            'class' => 'field-long',
            'required' => 'true',
            'minlength' => '1',
            'maxlength' => '32',
            'type' => 'text',
            'label' => __('Your name') . ' *',
            'autocomplete' => 'off',
        ],
    );
    echo $this->Form->control(
        'text',
        [
            'rows' => '5',
            'class' => 'field-long field-textarea',
            'required' => 'true',
            'minlength' => '3',
            'maxlength' => '512',
            'label' => __('Your comment') . ' *',
        ],
    );
    echo '<div class="content-right">';
    echo $this->Form->button(__('Save'));
    echo '</div>';
    echo $this->Form->end();
    ?>
</div>
