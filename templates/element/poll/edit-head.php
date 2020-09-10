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
    echo '<h1>' . __('Edit poll') . '</h1>';
    echo $this->Form->create(
        $poll, [
        'class' => 'form',
        'id' => 'form-new-poll',
        ]
    );
    echo $this->Form->control(
        'title', [
        'class' => 'field-long',
        'required' => 'true',
        'label' => __('Title') . ' *',
        'placeholder' => __('What about a title for your poll?'),
        ]
    );
    echo $this->Form->control(
        'details', [
        'rows' => '5',
        'class' => 'field-long field-textarea',
        'label' => __('Description'),
        'placeholder' => __('Your participants may also like a short description of what this poll is all about, right?'),
        ]
    );
    /*
    echo $this->Form->control(
        'email', [
        'class' => 'field-long',
        'label' => __('Email'),
        ]
    );
    */
    echo '<div class="content-right">';
    echo $this->Form->button(__('Save changes'));
    echo '</div>';
    echo $this->Form->end();
    ?>
