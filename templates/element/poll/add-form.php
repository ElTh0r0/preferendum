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
    ],
);
// Poll title
echo $this->Form->control(
    'title',
    [
        'class' => 'field-long',
        'required' => true,
        'label' => __('Title') . ' *',
        'placeholder' => __('Title for your poll'),
    ],
);
// Poll description
echo $this->Form->control(
    'details',
    [
        'rows' => '5',
        'class' => 'field-long field-textarea',
        'label' => __('Description'),
        'placeholder' => __('Short description of what this poll is all about'),
    ],
);
// Choices
$inputstyle = '';
if ($prefconf['opt_MaxEntriesPerOption']) {
    echo '<br>' . $this->Form->checkbox(
        'limitentry',
        [
            'id' => 'limitinput',
            'onchange' => 'toggleMaxEntryInput()',
        ],
    );
    echo '<span style="font-size: 90%;">' . __('Define max. number of entries/option (0 = unrestricted)') . '</span>';
    $inputstyle = ' float: left; margin-right: 8px; width: 342px';
}
echo $this->Form->control(
    'options',
    [
        'name' => 'choices[]',
        'maxlength' => '50',
        'class' => 'dateInput field-long datepicker-here',
        'required' => true,
        'label' => __('Options') . ' *',
        'placeholder' => __('Type whatever you want or pick a date!'),
        'style' => 'margin-bottom: 8px;' . $inputstyle,
    ],
);
if ($prefconf['opt_MaxEntriesPerOption']) {
    echo $this->Form->control(
        'max_entries',
        [
            'name' => 'max_entries[]',
            'class' => 'maxEntryInput',
            'id' => 'max-entriesinput',
            'label' => false,
            'style' => 'margin-bottom: 8px; width: 50px;',
            'type' => 'number',
            'value' => '0',
            'min' => 0,
            'max' => 99,
            'disabled' => true,
        ],
    );
}
echo '<div class="content-right">';
echo $this->Form->button(
    '',
    [
        'type' => 'button',
        'id' => 'btnMore',
    ],
);
echo ' ';
echo $this->Form->button(
    '',
    [
        'type' => 'button',
        'id' => 'btnLess',
        'disabled' => true,
    ],
);
echo '</div>';

// No 'Poll options' caption, if none of 'opt_*' configuration parameters is true
$filtered = array_filter($prefconf, function ($k) {
    return str_starts_with($k, 'opt_');
}, ARRAY_FILTER_USE_KEY);
if (in_array(1, $filtered, false)) {
    echo '<h2>' . __('Poll options') . '</h2>';
}

echo '<ul>';
// --------------------------------------------------------------
// Use admin link
if (
    $prefconf['opt_AdminLinks'] &&
    !$prefconf['alwaysUseAdminLinks']
) {
    echo '<li>';
    echo $this->Form->checkbox(
        'adminid',
        [
            'value' => 'true',
            'id' => 'admininput',
            'onchange' => 'toggleAdminLinkInput()',
            'checked' => true,
        ],
    );
    echo '<span style="font-size: 90%;">' . __('Edit/deleting poll/entries only with admin link') . '</span>';
    echo '</li>';
}

// --------------------------------------------------------------
// Hide poll votes
if (
    $prefconf['opt_HidePollVotes'] &&
    ($prefconf['opt_AdminLinks'] ||
        $prefconf['alwaysUseAdminLinks'])
) {
    echo '<li>';
    echo $this->Form->checkbox(
        'hidevotes',
        [
            'value' => 'true',
            'id' => 'hidevotesinput',
        ],
    );
    echo '<span style="font-size: 90%;">' . __('Hide poll votes for users (only admin can see the votes)') . '</span>';
    echo '</li>';
}

// --------------------------------------------------------------
// Allow to change entry
if (
    $prefconf['opt_AllowChangeEntry'] &&
    ($prefconf['opt_AdminLinks'] ||
        $prefconf['alwaysUseAdminLinks'])
) {
    echo '<li>';
    echo $this->Form->checkbox(
        'editentry',
        [
            'value' => 'true',
            'id' => 'editentryinput',
        ],
    );
    echo '<span style="font-size: 90%;">' . __('Users can modify their entry with a personal link') . '</span>';
    echo '</li>';
}

// --------------------------------------------------------------
// Collect user info
if (
    $prefconf['opt_CollectUserinfo'] &&
    $prefconf['adminInterface']
) {
    echo '<li>';
    echo $this->Form->checkbox(
        'userinfo',
        [
            'value' => 'true',
            'id' => 'userinfoinput',
            'onchange' => 'toggleUserinfoInput()',
        ],
    );
    echo '<span style="font-size: 90%;">' . __('Collect user contact information') . '</span>';
    echo '</li>';
}

// --------------------------------------------------------------
// Anonymous vote
if (
    $prefconf['opt_AnonymousVotes']
) {
    echo '<li>';
    echo $this->Form->checkbox(
        'anonymous',
        [
            'value' => 'true',
            'id' => 'anonymousinput',
        ],
    );
    echo '<span style="font-size: 90%;">' . __('Anonymous poll (no user name stored/shown)') . '</span>';
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
            'id' => 'pwprotectinput',
            'onchange' => 'togglePasswordInput()',
        ],
    );
    echo '<span style="font-size: 90%;">' . __('Protect poll access with a password') . '</span>';
    echo '</li>';
    echo '<li>';
    echo $this->Form->password(
        'password',
        [
            'class' => 'field-long',
            'id' => 'passwordinput',
            'disabled' => true,
            'placeholder' => __('Password'),
        ],
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
            'onchange' => 'toggleEmailInput()',
        ],
    );
    echo '<span style="font-size: 90%;">' . __('Allow users to add a comment') . '</span>';
    echo '</li>';
}

// --------------------------------------------------------------
// Label for email options
if (
    $prefconf['opt_SendPollCreationEmail'] ||
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
            'onchange' => 'toggleEmailInput()',
            'disabled' => (!$prefconf['alwaysAllowComments'] && $prefconf['opt_Comments']),
        ],
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
            'onchange' => 'toggleEmailInput()',
        ],
    );
    echo '<span style="font-size: 90%;">' . __('Receive email after new entry') . '</span>';
    echo '</li>';
}

// --------------------------------------------------------------
// Receive email after poll creation
if ($prefconf['opt_SendPollCreationEmail']) {
    echo '<li>';
    echo $this->Form->checkbox(
        'emailpoll',
        [
            'value' => 'true',
            'id' => 'emailpollinput',
            'onchange' => 'toggleEmailInput()',
        ],
    );
    echo '<span style="font-size: 90%;">' . __('Receive email after poll creation with poll links') . '</span>';
    echo '</li>';
}

// --------------------------------------------------------------
// Email textbox
if (
    $prefconf['opt_SendPollCreationEmail'] ||
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
            'disabled' => true,
            'placeholder' => __('Email for receiving entry/comment/poll links'),
            'autocomplete' => 'email',
        ],
    );
    if (!$prefconf['alwaysUseAdminLinks']) {
        echo '<div id="emailwarn" class="fail" style="display: none;"><p><span style="font-size: 80%;">' .
            __('Attention: If no admin link is used, the email address is visible for everyone!') . '</span></p></div>';
    }
    echo '</li>';
}

// --------------------------------------------------------------
// Poll expiry date
$demoMode = false;
if (Configure::read('preferendum.demoMode')) {
    $demoMode = true;
}

if ($prefconf['opt_PollExpirationAfter'] > 0 || $demoMode) {
    echo '<li>' . $this->Form->label('hasexpinput', __('Expiry date')) . '</li>';
    echo '<li>';
    echo $this->Form->checkbox(
        'hasexp',
        [
            'checked' => true,
            'disabled' => $demoMode,
            'value' => 'true',
            'id' => 'hasexpinput',
            'onchange' => 'toggleExpiryInput()',
        ],
    );
    echo '<span style="font-size: 90%;">' . __('Poll is automatically locked on expiry date') . '</span>';
    echo '</li>';

    $exp = new DateTime('NOW');
    if ($demoMode) {
        $exp->modify('+1 days');
    } else {
        $exp->modify('+' . $prefconf['opt_PollExpirationAfter'] . ' days');
    }
    echo '<li>';
    echo $this->Form->control(
        'expiry',
        [
            'class' => 'field-long',
            'id' => 'expinput',
            'disabled' => $demoMode,
            'value' => $exp,
            'label' => '',
            'style' => 'margin-bottom: 8px;',
        ],
    );
    echo '</li>';
}
// --------------------------------------------------------------
echo '</ul>';

echo '<div class="content-right">';
echo $this->Form->button(__('Create poll'));
echo '</div>';
echo $this->Form->end();
