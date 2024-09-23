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

use Cake\Core\Configure;
?>

<?php
$prefconf = Configure::read('preferendum');

echo $this->Form->create(
    $poll,
    [
        'class' => 'form',
        'id' => 'form-new-poll',
        'type' => 'post',
        'url' => ['controller' => 'Polls', 'action' => 'update', $poll->id, $adminid],
    ]
);
// Poll title
echo $this->Form->control(
    'title',
    [
        'class' => 'field-long',
        'required' => 'true',
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

// No 'Poll options' caption, if none of 'opt_*' configuration parameters is true
$filtered = array_filter($prefconf, function ($k) {
    return str_starts_with($k, 'opt_');
}, ARRAY_FILTER_USE_KEY);
if (in_array(1, $filtered, false)) {
    echo '<h2>' . __('Poll options') . '</h2>';
}

echo '<ul>';
// --------------------------------------------------------------
// Define max. number of entries per poll
if ($prefconf['opt_MaxEntriesPerOption']) {
    echo $this->Form->checkbox(
        'limitentry',
        [
            'id' => 'limitinput',
            'checked' => $poll->limitentry,
        ]
    );
    echo '<span style="font-size: 90%;">' . __('Define max. number of entries/option (0 = unrestricted)') . '</span>';
}
// --------------------------------------------------------------
// Hide poll votes
if (
    $prefconf['opt_HidePollVotes'] &&
    strcmp($poll->adminid, 'NA') != 0
) {
    echo '<li>';
    echo $this->Form->checkbox(
        'hidevotes',
        [
            'value' => 'true',
            'checked' => $poll->hidevotes,
        ]
    );
    echo '<span style="font-size: 90%;">' . __('Hide poll votes for users (only admin can see the votes)') . '</span>';
    echo '</li>';
}

// --------------------------------------------------------------
// Allow to change entry
if (
    $prefconf['opt_AllowChangeEntry'] &&
    strcmp($poll->adminid, 'NA') != 0
) {
    echo '<li>';
    echo $this->Form->checkbox(
        'editentry',
        [
            'value' => 'true',
            'checked' => $poll->editentry,
        ]
    );
    echo '<span style="font-size: 90%;">' . __('Users can modify their entry with a personal link') . '</span>';
    echo '</li>';
}

// --------------------------------------------------------------
// Collect user info
if (
    $prefconf['opt_CollectUserinfo'] &&
    $prefconf['adminInterface'] &&
    strcmp($poll->adminid, 'NA') != 0 &&
    !$poll->anonymous
) {
    echo '<li>';
    echo $this->Form->checkbox(
        'userinfo',
        [
            'value' => 'true',
            'checked' => $poll->userinfo,
        ]
    );
    echo '<span style="font-size: 90%;">' . __('Collect user contact information') . '</span>';
    echo '</li>';
}

// --------------------------------------------------------------
// Protect poll access with a password
if ($prefconf['opt_PollPassword']) {
    echo '<li>';
    echo $this->Form->checkbox(
        'pwprotect',
        [
            'value' => 'true',
            'checked' => $poll->pwprotect,
            'id' => 'pwprotectinput',
            'onchange' => 'togglePasswordInput()',
        ]
    );
    echo '<span style="font-size: 90%;">' . __('Protect poll access with a password') . '</span>';
    echo '</li>';
    echo '<li>';
    echo $this->Form->password(
        'password',
        [
            'class' => 'field-long',
            'id' => 'passwordinput',
            'disabled' => !$poll->pwprotect,
            'placeholder' => __('Password'),
        ]
    );
    echo '</li>';
}

// --------------------------------------------------------------
// Allow comment per poll
if (
    $prefconf['opt_Comments'] &&
    !$prefconf['alwaysAllowComments']
) {
    echo '<li>';
    echo $this->Form->checkbox(
        'comment',
        [
            'value' => 'true',
            'id' => 'commentinput',
            'checked' => $poll->comment,
            'onchange' => 'toggleEmailInput()',
        ]
    );
    echo '<span style="font-size: 90%;">' . __('Allow users to add a comment') . '</span>';
    echo '</li>';
}

// --------------------------------------------------------------
// Label for email options
if (
    $prefconf['opt_SendEntryEmail'] ||
    ($prefconf['opt_SendCommentEmail'] &&
        ($prefconf['alwaysAllowComments'] ||
            $prefconf['opt_Comments']))
) {
    echo '<li>' . $this->Form->label('emailinput', __('Email')) . '</li>';
}

// --------------------------------------------------------------
// Receive email after new comment
if (
    $prefconf['opt_SendCommentEmail'] &&
    ($prefconf['alwaysAllowComments'] ||
        $prefconf['opt_Comments'])
) {
    echo '<li>';
    echo $this->Form->checkbox(
        'emailcomment',
        [
            'value' => 'true',
            'id' => 'emailcommentinput',
            'checked' => $poll->emailcomment,
            'onchange' => 'toggleEmailInput()',
            'disabled' => (!$prefconf['alwaysAllowComments'] && $prefconf['opt_Comments']) &&
                ($prefconf['opt_Comments'] && !$poll->comment),
        ]
    );
    echo '<span style="font-size: 90%;">' . __('Receive email after new comment') . '</span>';
    echo '</li>';
}

// --------------------------------------------------------------
// Receive email after new entry
if ($prefconf['opt_SendEntryEmail']) {
    echo '<li>';
    echo $this->Form->checkbox(
        'emailentry',
        [
            'value' => 'true',
            'id' => 'emailentryinput',
            'checked' => $poll->emailentry,
            'onchange' => 'toggleEmailInput()',
        ]
    );
    echo '<span style="font-size: 90%;">' . __('Receive email after new entry') . '</span>';
    echo '</li>';
}

// --------------------------------------------------------------
// Email textbox
if (
    $prefconf['opt_SendEntryEmail'] ||
    ($prefconf['opt_SendCommentEmail'] &&
        ($prefconf['alwaysAllowComments'] ||
            $prefconf['opt_Comments']))
) {
    echo '<li>';
    echo $this->Form->control(
        'email',
        [
            'class' => 'field-long',
            'id' => 'emailinput',
            'label' => '',
            'disabled' => (!$poll->emailentry && !$poll->emailcomment),
            'placeholder' => __('Email for receiving new entry/comment'),
            'autocomplete' => 'email',
        ]
    );
    if (strcmp($poll->adminid, 'NA') == 0) {
        echo '<div class="fail"><p><span style="font-size: 80%;">' .
            __('Attention: If no admin link is used, the email address is visible for everyone!') . '</span></p></div>';
    }
    echo '</li>';
}

// --------------------------------------------------------------
// Poll expiry date
if ($prefconf['opt_PollExpirationAfter'] > 0) {
    $exp = $poll->expiry;
    $hasDate = true;
    if (!$exp) {
        $hasDate = false;
        $exp = new DateTime('NOW');
        $exp->modify('+' . $prefconf['opt_PollExpirationAfter'] . ' days');
    }

    echo '<li>' . $this->Form->label('hasexpinput', __('Expiry date')) . '</li>';
    echo '<li>';
    echo $this->Form->checkbox(
        'hasexp',
        [
            'checked' => $hasDate,
            'value' => 'true',
            'id' => 'hasexpinput',
            'onchange' => 'toggleExpiryInput()',
        ]
    );
    echo '<span style="font-size: 90%;">' . __('Poll is automatically locked on expiry date') . '</span>';
    echo '</li>';

    echo '<li>';
    echo $this->Form->control(
        'expiry',
        [
            'class' => 'field-long',
            'id' => 'expinput',
            'value' => $exp,
            'label' => '',
            'disabled' => !$hasDate,
            'style' => 'margin-bottom: 8px;',
        ]
    );
    echo '</li>';
}
// --------------------------------------------------------------
echo '</ul>';

echo '<div class="content-right">';
echo $this->Form->button(__('Save changes'));
echo '</div>';
echo $this->Form->end();
