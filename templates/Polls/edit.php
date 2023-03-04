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
<?php $this->assign('title', __('Edit poll') . ' - ' . $poll->title); ?>

<?php $this->Html->css('datepicker.min.css', ['block' => true]); ?>
<?php $this->Html->script('datepicker.min.js', ['block' => true]); ?>
<?php $this->Html->script('poll_create.js', ['block' => 'scriptBottom']); ?>
<?php $this->Html->script('poll_view.js', ['block' => 'scriptBottom']); ?>
<?php $this->Html->script('clipboard.min.js', ['block' => true]); ?>
<?php $this->Html->script('poll_options.js', ['block' => 'scriptBottom']); ?>

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

echo 'var jswebroot = ' . json_encode($this->request->getAttributes()['webroot']) . ';';
echo 'var jspollid = ' . json_encode($poll->id) . ';';
echo 'var jsadminid = ' . json_encode($adminid) . ';';
$this->Html->scriptEnd();

?>

<!-- POLL CONTROLS -->
<div id="poll-controls">
    <div>
        <?php echo $this->element('poll/ctrl-edit'); ?>
    </div>
</div>

<div class="center-box">
    <?php echo $this->Flash->render() ?>
</div>

<div class="center-box">
    <?php echo $this->element('poll/edit-head'); ?>
</div>

<div id="poll-container">
    <div class="center-box">
        <h1><?php echo __('Edit entries') ?></h1>
    </div>
    <table class="schedule">
        <?php
        echo $this->element('choice/edit');
        echo $this->element('entry/edit_view');
        echo '<tr class="table-spacer-row table-spacer-row-big"><td></td></tr>';

        if (\Cake\Core\Configure::read('preferendum.trendResult')) {
            echo $this->element('poll/result-trend');
        } else {
            echo $this->element('poll/result-simple');
        } ?>
    </table>
</div>

<?php if (
    \Cake\Core\Configure::read('preferendum.alwaysAllowComments')
    || (\Cake\Core\Configure::read('preferendum.opt_Comments') && $poll->comment)
) {
    echo '<div id="comments-wrapper">';
    echo $this->element('comment/delete');
    echo '</div>';
} ?>
