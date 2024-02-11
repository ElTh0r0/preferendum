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
 * @version   0.6.0
 */
?>

<?php
$this->assign('title', __('Edit poll') . ' - ' . $poll->title);

echo $this->element('poll/datepicker');
$this->Html->script('poll_create.js', ['block' => 'scriptBottom']);
$this->Html->script('poll_options.js', ['block' => 'scriptBottom']);
$this->Html->script('poll_view.js', ['block' => 'scriptBottom']);
$this->Html->script('clipboard.min.js', ['block' => true]);

$this->Html->scriptStart(['block' => true]);
echo 'var jswebroot = ' . json_encode($this->request->getAttributes()['webroot']) . ';';
echo 'var jspollid = ' . json_encode($poll->id) . ';';
echo 'var jsadminid = ' . json_encode($adminid) . ';';
echo 'var jsNo = ' . json_encode(__('No')) . ';';
echo 'var jsYes = ' . json_encode(__('Yes')) . ';';
echo 'var jsMaybe = ' . json_encode(__('Maybe')) . ';';
$this->Html->scriptEnd();
?>

<div id="control-elements">
    <div>
        <?php echo $this->element('poll/ctrl-edit'); ?>
    </div>
</div>

<div class="center-box">
    <?php
    echo '<h1>' . __('Edit poll') . '</h1>';
    echo $this->Flash->render();

    echo $this->element('poll/edit-form');
    ?>
</div>

<div id="poll-container">
    <div class="center-box">
        <h1><?php echo __('Edit entries') ?></h1>
    </div>
    <table class="schedule">
        <?php
        echo $this->element('choice/edit');
        echo $this->element('entry/edit_view');
        echo '<tr class="table-spacer-row table-spacer-row-big"><td colspan="' . (sizeof($pollchoices) + 2) . '"></td></tr>';

        $resultVisual = \Cake\Core\Configure::read('preferendum.resultVisualization');
        if (0 != strcmp('none', $resultVisual)) {
            if (file_exists(Cake\Core\App::path('templates')[0] . 'element/poll/result-' . $resultVisual . '.php')) {
                echo $this->element('poll/result-' . $resultVisual);
            }
        }
        ?>
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
