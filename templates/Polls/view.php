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
<?php $this->assign('title', __('Poll') . ' - ' . $poll->title); ?>

<?php $this->Html->script('poll_view.js', ['block' => 'scriptBottom']); ?>
<?php $this->Html->script('clipboard.min.js', ['block' => true]); ?>

<?php
$this->Html->scriptStart(['block' => true]);
echo 'var jswebroot = ' . json_encode($this->request->getAttributes()['webroot']) . ';';
echo 'var jspollid = ' . json_encode($poll->pollid) . ';';
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
        <?php echo $this->element('entry/list'); ?>

        <!-- SPACER ROW -->
        <?php echo '<tr class="table-spacer-row"><td colspan="' . (sizeof($poll->choices) + 1) . '"></td></tr>'; ?>

        <!-- NEW ENTRY FORM ROW -->
        <?php if ($poll->locked == 0) {
            echo '<tr class="schedule-new valign-middle">';
            echo $this->element('entry/new');
            echo '</tr>';

            echo '<tr class="table-spacer-row table-spacer-row-big"><td></td></tr>';
        } ?>

        <!-- RESULTS -->
        <?php if (\Cake\Core\Configure::read('Sprudel-ng.trendResult')) {
            echo $this->element('poll/result-trend');
        } else {
            echo $this->element('poll/result-simple');
        } ?>
    </table>
</div>

<!-- COMMENTS VIEW -->
<div id="comments-wrapper">
    <!-- COMMENTS LIST -->
    <?php echo $this->element('comment/list'); ?>

    <!-- COMMENTS FORM -->
    <?php if ($poll->locked == 0) {
        echo $this->element('comment/new');
    } ?>
</div>
