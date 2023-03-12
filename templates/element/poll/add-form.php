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
echo $this->Form->create(
    $poll,
    [
        'class' => 'form',
        'id' => 'form-new-poll',
    ]
);
// Poll title
echo $this->Form->control(
    'title',
    [
        'class' => 'field-long',
        'required' => true,
        'label' => __('Title') . ' *',
        'placeholder' => __('Title for your poll'),
    ]
);
// Poll description
echo $this->Form->control(
    'details',
    [
        'rows' => '5',
        'class' => 'field-long field-textarea',
        'label' => __('Description'),
        'placeholder' => __('Short description of what this poll is all about'),
    ]
);
// Choices
echo $this->Form->control(
    'options',
    [
        'name' => 'choices[]',
        'maxlength' => '32',
        'class' => 'dateInput field-long datepicker-here',
        'required' => true,
        'label' => __('Options') . ' *',
        'placeholder' => __('Type whatever you want or pick a date!'),
        'style' => 'margin-bottom: 8px;',
    ]
);
echo '<div class="content-right">';
echo $this->Form->button(
    '',
    [
        'type' => 'button',
        'id' => 'btnMore',
    ]
);
echo ' ';
echo $this->Form->button(
    '',
    [
        'type' => 'button',
        'id' => 'btnLess',
        'disabled' => true,
    ]
);
echo '</div>';

echo '<ul>';
// --------------------------------------------------------------
// Use admin link
if (
    \Cake\Core\Configure::read('preferendum.opt_AdminLinks') &&
    !(\Cake\Core\Configure::read('preferendum.alwaysUseAdminLinks'))
) {
    echo '<li>';
    echo $this->Form->checkbox(
        'adminid',
        [
            'value' => 'true',
            'id' => 'adminInput',
            'onchange' => 'toggleAdminLinkInput()',
            'checked' => true,
        ]
    );
    echo '<span style="font-size: 90%;">' . __('Edit/deleting poll/entries only with admin link') . '</span>';
    echo '</li>';
}

// --------------------------------------------------------------
// Hide poll result
if (
    \Cake\Core\Configure::read('preferendum.opt_HidePollResult') &&
    (\Cake\Core\Configure::read('preferendum.opt_AdminLinks') ||
        \Cake\Core\Configure::read('preferendum.alwaysUseAdminLinks'))
) {
    echo '<li>';
    echo $this->Form->checkbox(
        'hideresult',
        [
            'value' => 'true',
            'id' => 'hideresultInput',
        ]
    );
    echo '<span style="font-size: 90%;">' . __('Hide poll results for users (only admin can see the votes)') . '</span>';
    echo '</li>';
}

// --------------------------------------------------------------
// Allow to change entry
if (
    \Cake\Core\Configure::read('preferendum.opt_AllowChangeEntry') &&
    (\Cake\Core\Configure::read('preferendum.opt_AdminLinks') ||
        \Cake\Core\Configure::read('preferendum.alwaysUseAdminLinks'))
) {
    echo '<li>';
    echo $this->Form->checkbox(
        'editentry',
        [
            'value' => 'true',
            'id' => 'editentryInput',
        ]
    );
    echo '<span style="font-size: 90%;">' . __('Users can modify their entry with a personal link') . '</span>';
    echo '</li>';
}

// --------------------------------------------------------------
// Collect user info
if (
    \Cake\Core\Configure::read('preferendum.opt_CollectUserinfo') &&
    \Cake\Core\Configure::read('preferendum.adminInterface')
) {
    echo '<li>';
    echo $this->Form->checkbox(
        'userinfo',
        [
            'value' => 'true',
        ]
    );
    echo '<span style="font-size: 90%;">' . __('Collect user contact information') . '</span>';
    echo '</li>';
}

// --------------------------------------------------------------
// Protect poll access with a password
if (\Cake\Core\Configure::read('preferendum.opt_PollPassword')) {
    echo '<li>';
    echo $this->Form->checkbox(
        'pwprotect',
        [
            'value' => 'true',
            'id' => 'pwprotectInput',
            'onchange' => 'togglePasswordInput()',
        ]
    );
    echo '<span style="font-size: 90%;">' . __('Protect poll access with a password') . '</span>';
    echo '</li>';
    echo '</ul>';
    echo $this->Form->password(
        'password',
        [
            'class' => 'field-long',
            'id' => 'passwordInput',
            'disabled' => true,
            'placeholder' => __('Password'),
        ]
    );
}

echo '<ul>';
// --------------------------------------------------------------
// Allow comment per poll
if (
    \Cake\Core\Configure::read('preferendum.opt_Comments')
    && !\Cake\Core\Configure::read('preferendum.alwaysAllowComments')
) {
    echo '<li>';
    echo $this->Form->checkbox(
        'comment',
        [
            'value' => 'true',
            'id' => 'commentInput',
            'onchange' => 'toggleEmailInput()',
        ]
    );
    echo '<span style="font-size: 90%;">' . __('Allow users to add a comment') . '</span>';
    echo '</li>';
}

// --------------------------------------------------------------
// Receive email after new comment
if (
    \Cake\Core\Configure::read('preferendum.opt_SendCommentEmail')
    && (\Cake\Core\Configure::read('preferendum.alwaysAllowComments')
        || \Cake\Core\Configure::read('preferendum.opt_Comments'))
) {
    echo '<li>';
    echo $this->Form->checkbox(
        'emailcomment',
        [
            'value' => 'true',
            'id' => 'emailcommentInput',
            'onchange' => 'toggleEmailInput()',
            'disabled' => (!\Cake\Core\Configure::read('preferendum.alwaysAllowComments') && \Cake\Core\Configure::read('preferendum.opt_Comments')),
        ]
    );
    echo '<span style="font-size: 90%;">' . __('Receive email after new comment') . '</span>';
    echo '</li>';
}

// --------------------------------------------------------------
// Receive email after new entry
if (\Cake\Core\Configure::read('preferendum.opt_SendEntryEmail')) {
    echo '<li>';
    echo $this->Form->checkbox(
        'emailentry',
        [
            'value' => 'true',
            'id' => 'emailentryInput',
            'onchange' => 'toggleEmailInput()',
        ]
    );
    echo '<span style="font-size: 90%;">' . __('Receive email after new entry') . '</span>';
    echo '</li>';
}
echo '</ul>';

// --------------------------------------------------------------
// Email textbox
if (
    \Cake\Core\Configure::read('preferendum.opt_SendEntryEmail')
    || (\Cake\Core\Configure::read('preferendum.opt_SendCommentEmail')
        && (\Cake\Core\Configure::read('preferendum.alwaysAllowComments')
            || \Cake\Core\Configure::read('preferendum.opt_Comments')))
) {
    echo $this->Form->control(
        'email',
        [
            'class' => 'field-long',
            'id' => 'emailInput',
            'label' => __('Email'),
            'disabled' => true,
            'placeholder' => __('Email for receiving new entry/comment'),
        ]
    );
}

echo '<div class="content-right">';
echo $this->Form->button(__('Create poll'));
echo '</div>';
echo $this->Form->end();
?>
