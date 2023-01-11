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
<?php $this->assign('title', __('Create poll')); ?>

<?php $this->Html->css('datepicker.min.css', ['block' => true]); ?>
<?php $this->Html->script('datepicker.min.js', ['block' => true]); ?>
<?php $this->Html->script('poll_create.js', ['block' => 'scriptBottom']); ?>

<?php
use Cake\I18n\I18n;
$this->Html->scriptStart(['block' => true]);
// Datepicker localizations
echo 'var jslocale = ' . json_encode(I18n::getLocale()) . ';';
echo 'var jsdateformat = ' . json_encode(
    \Cake\Core\Configure::read('preferendum.datepickerFormat')
) . ';';
echo 'var jsmonday = ' . json_encode(__('Monday')) . ';';
echo 'var jstuesday = ' . json_encode(__('Tuesday')) . ';';
echo 'var jswednesday = ' . json_encode(__('Wednesday')) . ';';
echo 'var jsthursday = ' . json_encode(__('Thursday')) . ';';
echo 'var jsfriday = ' . json_encode(__('Friday')) . ';';
echo 'var jssaturday = ' . json_encode(__('Saturday')) . ';';
echo 'var jssunday = ' . json_encode(__('Sunday')) . ';';
// Maximum number of options
echo 'var jsmaxoptions = ' . json_encode(
    \Cake\Core\Configure::read('preferendum.maxPollOptions')
) . ';';
$this->Html->scriptEnd();
?>

<div class="center-box">
    <h1><?php echo __('Create a new poll ...') ?></h1>
    <?php echo $this->Flash->render() ?>

    <?php
    echo $this->Form->create(
        $poll, [
        'class' => 'form',
        'id' => 'form-new-poll',
        ]
    );
    echo $this->Form->control(
        'title', [
        'class' => 'field-long',
        'required' => true,
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

    echo $this->Form->control(
        'options', [
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
        '', [
        'type' => 'button',
        'id' => 'btnMore',
        ]
    );
    echo ' ';
    echo $this->Form->button(
        '', [
        'type' => 'button',
        'id' => 'btnLess',
        'disabled' => true,
        ]
    );
    echo '</div>';

    echo '<ul>';
    if (\Cake\Core\Configure::read('preferendum.adminLinks')) {
        echo '<li>';
        echo $this->Form->checkbox(
            'adminid', [
            'value' => 'true',
            'id' => 'adminInput',
            'onchange' => 'toggleHideResultInput()',
            'checked' => true,
            ]
        );
        echo '<span style="font-size: 90%;">' . __('Edit/deleting poll/entries only with admin link') . '</span>';
        echo '</li>';
    }
    
    echo '<li>';
    echo $this->Form->checkbox(
        'hideresult', [
        'value' => 'true',
        'id' => 'hideresultInput',
        'hidden' => !(\Cake\Core\Configure::read('preferendum.hidePollResult') && \Cake\Core\Configure::read('preferendum.adminLinks')),
        ]
    );
    if (\Cake\Core\Configure::read('preferendum.hidePollResult') && \Cake\Core\Configure::read('preferendum.adminLinks')) {
        echo '<span style="font-size: 90%;">' . __('Hide poll results for users (only admin can see the votes)') . '</span>';
    }
    echo '</li>';

    if (\Cake\Core\Configure::read('preferendum.collectUserinfo') && \Cake\Core\Configure::read('preferendum.adminInterface')) {
        echo '<li>';
        echo $this->Form->checkbox(
            'userinfo', [
            'value' => 'true',
            ]
        );
        echo '<span style="font-size: 90%;">' . __('Collect user contact information') . '</span>';
        echo '</li>';
    }

    echo '<li>';
    echo $this->Form->checkbox(
        'emailentry', [
        'value' => 'true',
        'id' => 'emailentryInput',
        'onchange' => 'toggleEmailInput()',
        'hidden' => !(\Cake\Core\Configure::read('preferendum.sendEntryEmail')),
        ]
    );
    if (\Cake\Core\Configure::read('preferendum.sendEntryEmail')) {
        echo '<span style="font-size: 90%;">' . __('Receive email after new entry') . '</span>';
    }
    echo '</li>';
    
    echo '<li>';
    echo $this->Form->checkbox(
        'emailcomment', [
        'value' => 'true',
        'id' => 'emailcommentInput',
        'onchange' => 'toggleEmailInput()',
        'hidden' => !(\Cake\Core\Configure::read('preferendum.sendCommentEmail')),
        ]
    );
    if (\Cake\Core\Configure::read('preferendum.sendCommentEmail')) {
        echo '<span style="font-size: 90%;">' . __('Receive email after new comment') . '</span>';
    }
    echo '</li>';
    echo '</ul>';

    if (\Cake\Core\Configure::read('preferendum.sendEntryEmail') 
        || \Cake\Core\Configure::read('preferendum.sendCommentEmail')
    ) {
        echo $this->Form->control(
            'email', [
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

    $deleteInactive = \Cake\Core\Configure::read('preferendum.deleteInactivePollsAfter');
    if ($deleteInactive > 0) {
        echo '<p><br /><span class="pale">';
        echo __('Attention! Inactive polls will be deleted automatically after {0} days!', $deleteInactive);
        echo '</span></p>';
    }
    ?>
</div>
