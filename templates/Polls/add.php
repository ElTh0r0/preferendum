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
    \Cake\Core\Configure::read('Sprudel-ng.datepickerFormat')
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
    \Cake\Core\Configure::read('Sprudel-ng.maxPollOptions')
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
        'disabled',
        ]
    );
    echo '</div>';

    echo '<ul>';
    if (\Cake\Core\Configure::read('Sprudel-ng.adminLinks')) {
        echo '<li>';
        echo $this->Form->checkbox(
            'adminLink', [
            'value' => 'true',
            'id' => 'adminInput',
            'checked',
            ]
        );
        echo '<span style="font-size: 90%;">' . __('Deleting poll/entries only with admin link') . '</span>';
        echo '</li>';
    }
    
    if (\Cake\Core\Configure::read('Sprudel-ng.collectUserinfo') && \Cake\Core\Configure::read('Sprudel-ng.adminInterface')) {
        echo '<li>';
        echo $this->Form->checkbox(
            'userinfo', [
            'value' => 'true',
            ]
        );
        echo '<span style="font-size: 90%;">' . __('Collect user contact information') . '</span>';
        echo '</li>';
    }

    /*
    echo '<li>';
    echo $this->Form->checkbox(
    'emailentry', [
        'value' => 'true',
        'id' => 'emailentryInput',
    ]
    );
    echo '<span style="font-size: 90%;">' . __('Receive email after new entry') . '</span>';
    echo '</li>';
        
    echo '<li>';
    echo $this->Form->checkbox(
    'emailcomment', [
    'value' => 'true',
        'id' => 'emailcommentInput',
    ]
    );
    echo '<span style="font-size: 90%;">' . __('Receive email after new comment') . '</span>';
    echo '</li>';
    */
    echo '</ul>';

    /*
    echo $this->Form->control(
    'email', [
        'class' => 'field-long',
        'id' => 'emailInput',
        'label' => __('Email'),
    ]
    );
    */

    echo '<div class="content-right">';
    echo $this->Form->button(__('Create poll'));
    echo '</div>';
    echo $this->Form->end();

    $deleteInactive = \Cake\Core\Configure::read('Sprudel-ng.deleteInactivePollsAfter');
    if ($deleteInactive > 0) {
        echo '<p><br /><span class="pale">';
        echo __('Attention! Inactive polls will be deleted automatically after {0} days!', $deleteInactive);
        echo '</span></p>';
    }
    ?>
</div>
