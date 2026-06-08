<?php

/**
 * PREFERendum
 *
 * SPDX-FileCopyrightText: codeberg.org/ElTh0r0
 * SPDX-License-Identifier: MIT
 *
 * @copyright 2020-present codeberg.org/ElTh0r0
 * @license   MIT License (https://opensource.org/license/MIT)
 * @link      https://codeberg.org/ElTh0r0/preferendum
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
