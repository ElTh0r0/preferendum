<?php

/**
 * PREFERendum (https://github.com/ElTh0r0/preferendum)
 * Copyright (c) github.com/ElTh0r0
 *
 * Licensed under The MIT License
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright 2020-present github.com/ElTh0r0
 * @license   MIT License (https://opensource.org/licenses/mit-license.php)
 * @link      https://github.com/ElTh0r0/preferendum
 */
?>
<?php $this->assign('title', __('Poll administration')); ?>

<div id="control-elements">
    <div>
        <?php echo $this->element('admin/ctrl-login'); ?>
    </div>
</div>

<?php
if (isset($pollid)) { ?>
    <div class="center-box">
        <div class="message">
            <?php echo __('This poll is password protected!') ?><br>
            <?php echo __('Please enter the password and press "Login".') ?>
        </div>
    </div>
<?php } ?>
