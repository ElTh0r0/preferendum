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
<?php $this->assign('title', __('Poll') . ' - ' . $poll->title); ?>

<?php $this->Html->script('poll_view.js', ['block' => 'scriptBottom']); ?>
<?php $this->Html->script('clipboard.min.js', ['block' => true]); ?>

<?php
$this->Html->scriptStart(['block' => true]);
echo 'var jswebroot = ' . json_encode($this->request->getAttributes()['webroot']) . ';';
echo 'var jspollid = ' . json_encode($poll->id) . ';';
echo 'var jsadminid = ' . json_encode($adminid) . ';';
echo 'var jsMini = ' . json_encode(__('Mini View')) . ';';
echo 'var jsNormal = ' . json_encode(__('Normal View')) . ';';
$this->Html->scriptEnd();
?>

<!-- POLL CONTROLS -->
<div id="poll-controls">
    <div>
        <?php echo $this->element('poll/ctrl'); ?>
    </div>
</div>

<div class="center-box">
    <?php echo $this->Flash->render() ?>
</div>

<div id="poll-container">
    <table class="schedule">
        <?php echo $this->element('choice/list'); ?>
        <?php echo $this->element('entry/list'); ?>

        <!-- SPACER ROW -->
        <?php echo '<tr class="table-spacer-row"><td colspan="' . (sizeof($pollchoices) + 1) . '"></td></tr>'; ?>

        <!-- NEW ENTRY FORM ROW -->
        <?php if ($poll->locked == 0) {
            echo '<tr class="schedule-new valign-middle">';
            if (!isset($userpw)) {
                echo $this->element('entry/new');
            } else {
                echo $this->element('entry/edit');
            }
            echo '</tr>';

            echo '<tr class="table-spacer-row table-spacer-row-big"><td></td></tr>';
        } ?>

        <!-- RESULTS -->
        <?php if ($poll->hideresult == 0) {
            if (\Cake\Core\Configure::read('preferendum.trendResult')) {
                echo $this->element('poll/result-trend');
            } else {
                echo $this->element('poll/result-simple');
            }
        } ?>
    </table>
</div>

<?php if (
    \Cake\Core\Configure::read('preferendum.alwaysAllowComments')
    || (\Cake\Core\Configure::read('preferendum.opt_Comments') && $poll->comment)
) {
    echo '<div id="comments-wrapper">';
    if ($poll->hideresult == 0) {
        echo $this->element('comment/list');
    }
    if ($poll->locked == 0) {
        echo $this->element('comment/new');
    }
    echo '</div>';
} ?>
