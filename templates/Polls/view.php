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

use Cake\Core\App;
use Cake\Core\Configure;
?>

<?php
$this->assign('title', __('Poll') . ' - ' . $poll->title);

if (
    !isset($adminid) ||
    (!strcmp($poll->adminid, $adminid) == 0)
) {
    $adminid = null;
}

$this->Html->script('poll_view.js', ['block' => 'scriptBottom']);
$this->Html->script('clipboard.min.js', ['block' => true]);

$this->Html->scriptStart(['block' => true]);
echo 'var jswebroot = ' . json_encode($this->request->getAttributes()['webroot']) . ';';
echo 'var jspollid = ' . json_encode($poll->id) . ';';
echo 'var jsadminid = ' . json_encode($adminid) . ';';
echo 'var jsMini = ' . json_encode(__('Mini View')) . ';';
echo 'var jsNormal = ' . json_encode(__('Normal View')) . ';';
echo 'var jsNo = ' . json_encode(__('No')) . ';';
echo 'var jsYes = ' . json_encode(__('Yes')) . ';';
echo 'var jsMaybe = ' . json_encode(__('Maybe')) . ';';
$this->Html->scriptEnd();
?>

<div id="control-elements">
    <div>
        <?php echo $this->element('poll/ctrl'); ?>
    </div>
</div>

<div class="center-box">
    <?php echo $this->Flash->render() ?>
</div>

<div id="poll-container">
    <?php if ($poll->locked == 0) {
        if (isset($userpw) && $poll->editentry == 1) { // Edit entry
            $edituser = '';
            if (in_array($userpw, $usermap_pw)) {
                $edituser = array_search($userpw, $usermap_pw);
            }

            echo $this->Form->create(
                $newentry,
                [
                    'type' => 'post',
                    'id' => 'entry_form',
                    'url' => ['controller' => 'Entries', 'action' => 'edit', $poll->id, $usermap[$edituser], $userpw, $adminid],
                ],
            );
        } else { // New entry
            echo $this->Form->create(
                $newentry,
                [
                    'type' => 'post',
                    'id' => 'entry_form',
                    'url' => ['controller' => 'Entries', 'action' => 'new', $poll->id],
                ],
            );
        }
    } ?>

    <table class="schedule">
        <?php echo $this->element('choice/list'); ?>
        <?php if ($poll->hidevotes == 0) {
            echo $this->element('entry/list');
        } ?>

        <!-- SPACER ROW -->
        <?php echo '<tr class="table-spacer-row"><td colspan="' . (count($pollchoices) + 2) . '"></td></tr>'; ?>

        <!-- NEW ENTRY FORM ROW -->
        <?php if ($poll->locked == 0) {
            if (isset($userpw) && $poll->editentry == 1) {
                echo $this->element('entry/edit');
            } else {
                echo $this->element('entry/new');
            }

            echo '<tr class="table-spacer-row table-spacer-row-big">
            <td colspan="' . (count($pollchoices) + 2) . '"></td></tr>';
        } ?>

        <!-- RESULTS -->
        <?php if ($poll->hidevotes == 0) {
            $resultVisual = Configure::read('preferendum.resultVisualization');
            if (strcmp('none', $resultVisual) != 0) {
                if (file_exists(App::path('templates')[0] . 'element/poll/result-' . $resultVisual . '.php')) {
                    echo $this->element('poll/result-' . $resultVisual);
                }
            }
        } ?>
    </table>

    <?php if ($poll->locked == 0) {
        echo $this->Form->end();
    } ?>
</div>

<?php if (
    Configure::read('preferendum.alwaysAllowComments') ||
    (Configure::read('preferendum.opt_Comments') && $poll->comment)
) {
    echo '<div id="comments-wrapper">';
    if ($poll->hidevotes == 0) {
        echo $this->element('comment/list');
    }
    if ($poll->locked == 0) {
        echo $this->element('comment/new');
    }
    echo '</div>';
} ?>
