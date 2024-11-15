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
 * @version   0.8.0
 */

use Cake\Core\Configure;
?>

<?php
$this->assign('title', __('Create poll'));

$this->Html->script('poll_create.js', ['block' => 'scriptBottom']);
$this->Html->script('poll_options.js', ['block' => 'scriptBottom']);
echo $this->element('poll/datepicker');

$this->Html->scriptStart(['block' => true]);
// Maximum number of options
echo 'var jsmaxoptions = ' . json_encode(Configure::read('preferendum.maxPollOptions')) . ';';
$this->Html->scriptEnd();
?>

<?php if (
    Configure::read('preferendum.adminInterface') &&
    Configure::read('preferendum.restrictPollCreation')
) { ?>
    <div id="control-elements">
        <div>
            <?php echo $this->element('admin/ctrl-logout'); ?>
        </div>
    </div>
<?php } else { ?>
    <div id="control-elements">
        <div>
            <?php echo $this->element('poll/ctrl-new'); ?>
        </div>
    </div>
<?php } ?>

<div class="center-box">
    <?php
    echo '<h1>' . __('Create a new poll ...') . '</h1>';
    echo $this->Flash->render();

    echo $this->element('poll/add-form');

    $deleteInactive = Configure::read('preferendum.deleteInactivePollsAfter');
    if ($deleteInactive > 0) {
        echo '<p><br /><span class="pale">';
        echo __('Attention! Inactive polls will be deleted automatically after {0} days!', $deleteInactive);
        echo '</span></p>';
    }
    ?>
</div>
