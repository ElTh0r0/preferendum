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
<?php $this->assign('title', __('Edit poll') . ' - ' . $poll->title); ?>

<?php $this->Html->script('poll_view.js', ['block' => 'scriptBottom']); ?>
<?php $this->Html->script('clipboard.min.js', ['block' => true]); ?>

<?php
$this->Html->scriptStart(['block' => true]);
echo 'var jswebroot = ' . json_encode($this->request->getAttributes()['webroot']) . ';';
echo 'var jspollid = ' . json_encode($poll->pollid) . ';';
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
        <?php echo $this->element('entry/edit'); ?>
    </table>
</div>

<!-- COMMENTS VIEW -->
<div id="comments-wrapper">
    <!-- COMMENTS LIST -->
    <?php echo $this->element('comment/delete'); ?>
</div>
