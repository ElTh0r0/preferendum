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

use Cake\Core\Configure;
?>

<!-- MINI VIEW TOGGLE -->
<button id="ctrl-mini-view" type="button" data-miniview="off">
    <?php echo __('Mini View') ?>
</button>
<!-- EDIT POLL BUTTON -->
<?php
if (strcmp($poll->adminid, $adminid) == 0) {
    echo $this->Html->link(
        __('Edit'),
        ['action' => 'edit', $poll->id, $adminid],
        [
            'class' => 'button',
            'id' => 'ctrl-edit-poll',
            'escape' => false,
        ],
    );
}
if ($poll->pwprotect) {
    echo $this->Form->postLink(
        __('Logout'),
        ['controller' => 'Admin', 'action' => 'logout', $poll->id, $adminid],
        [
            'class' => 'button',
            'id' => 'ctrl-logout',
            'escape' => false,
        ],
    );
}
?>

<?php if (Configure::read('preferendum.toggleTheme')) { ?>
    <button type="button" class="themeToggle" data-theme-toggle>&nbsp;</button>
<?php } ?>
