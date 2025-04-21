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
// Go back to poll view
echo $this->Html->link(
    __('View Poll'),
    ['action' => 'view', $poll->id, $adminid],
    [
        'class' => 'button',
        'id' => 'ctrl-view-poll',
        'escape' => false,
    ],
);

// Un-/lock poll button
echo $this->Form->postLink(
    __('Un-/Lock'),
    ['action' => 'togglelock', $poll->id, $adminid],
    [
        'class' => 'button',
        'id' => 'ctrl-lock-poll',
        'escape' => false,
    ],
);

// Export CSV button
if (Configure::read('preferendum.exportCsv')) {
    echo $this->Form->postLink(
        __('CSV export'),
        ['action' => 'exportcsv', $poll->id, $adminid],
        [
            'class' => 'button',
            'id' => 'ctrl-export-poll',
            'escape' => false,
        ],
    );
}

// Delete poll button
echo $this->Form->postLink(
    __('Delete'),
    ['action' => 'delete', $poll->id, $adminid],
    [
        'class' => 'button',
        'id' => 'ctrl-delete-poll',
        'confirm' => __('Are you sure to delete this poll?'),
        'escape' => false,
    ],
);

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
