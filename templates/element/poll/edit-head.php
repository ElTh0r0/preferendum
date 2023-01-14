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
        'placeholder' => __('Title for your poll'),
        ]
    );
    echo $this->Form->control(
        'details', [
        'rows' => '5',
        'class' => 'field-long field-textarea',
        'label' => __('Description'),
        'placeholder' => __('Short description of what this poll is all about'),
        ]
    );
    
    echo '<ul>';
    if (\Cake\Core\Configure::read('preferendum.opt_HidePollResult') &&
        strcmp($poll->adminid, "NA") != 0
    ) {
        echo '<li>';
        echo $this->Form->checkbox(
            'hideresult', [
            'value' => 'true',
            'checked' => $poll->hideresult,
            ]
        );
        echo '<span style="font-size: 90%;">' . __('Hide poll results for users (only admin can see the votes)') . '</span>';
        echo '</li>';
    }

    if (\Cake\Core\Configure::read('preferendum.opt_CollectUserinfo') &&
        \Cake\Core\Configure::read('preferendum.adminInterface')
    ) {
        echo '<li>';
        echo $this->Form->checkbox(
            'userinfo', [
            'value' => 'true',
            'checked' => $poll->userinfo,
            ]
        );
        echo '<span style="font-size: 90%;">' . __('Collect user contact information') . '</span>';
        echo '</li>';
    }

    if (\Cake\Core\Configure::read('preferendum.opt_SendEntryEmail')) {
        echo '<li>';
        echo $this->Form->checkbox(
            'emailentry', [
            'value' => 'true',
            'id' => 'emailentryInput',
            'checked' => $poll->emailentry,
            'onchange' => 'toggleEmailInput()',
            ]
        );
        echo '<span style="font-size: 90%;">' . __('Receive email after new entry') . '</span>';
        echo '</li>';
    }

    if (\Cake\Core\Configure::read('preferendum.opt_SendCommentEmail')) {
        echo '<li>';
        echo $this->Form->checkbox(
            'emailcomment', [
            'value' => 'true',
            'id' => 'emailcommentInput',
            'checked' => $poll->emailcomment,
            'onchange' => 'toggleEmailInput()',
            ]
        );
        echo '<span style="font-size: 90%;">' . __('Receive email after new comment') . '</span>';
        echo '</li>';
    }
    echo '</ul>';

    if (\Cake\Core\Configure::read('preferendum.opt_SendEntryEmail') 
        || \Cake\Core\Configure::read('preferendum.opt_SendCommentEmail')
    ) {
        echo $this->Form->control(
            'email', [
            'class' => 'field-long',
            'id' => 'emailInput',
            'label' => __('Email'),
            'disabled' => (!$poll->emailentry & !$poll->emailcomment),
            'placeholder' => __('Email for receiving new entry/comment'),
            ]
        );
    }

    echo '<div class="content-right">';
    echo $this->Form->button(__('Save changes'));
    echo '</div>';
    echo $this->Form->end();
    ?>
