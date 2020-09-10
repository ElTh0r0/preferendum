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

<!-- MINI VIEW TOGGLE -->
<button id="ctrl-mini-view" type="button" data-miniview="off">
    <?php echo __('Mini View') ?>
</button>
<!-- EDIT POLL BUTTON -->
<?php
if (strcmp($poll->adminid, $adminid) == 0) {
    echo $this->Html->link(
        $this->Form->button(__('Edit'), ['type' => 'button', 'id' => 'ctrl-edit-poll']),
        ['action' => 'edit', $poll->pollid, $adminid],
        ['escape' => false]
    );
}
?>
