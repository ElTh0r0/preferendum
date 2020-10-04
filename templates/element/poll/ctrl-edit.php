<?php
/**
 * PREFERendum (https://github.com/ElTh0r0/preferendum)
 * Copyright (c) github.com/ElTh0r0, github.com/bkis
 *
 * Licensed under The MIT License
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright 2019-2020 github.com/ElTh0r0, github.com/bkis
 * @license   MIT License (https://opensource.org/licenses/mit-license.php)
 * @link      https://github.com/ElTh0r0/preferendum
 * @since     0.3.0
 */
?>

<!-- GO BACK TO POLL VIEW -->
<?php echo $this->Html->link(
    $this->Form->button(
        __('View Poll'), [
        'type' => 'button', 'id' => 'ctrl-mini-view']
    ),
    ['action' => 'view', $poll->pollid, $adminid],
    ['escape' => false]
); ?>

<!-- UN-/LOCK POLL BUTTON -->
<?php echo $this->Form->postLink(
    $this->Form->button(
        __('Un-/Lock'), [
        'type' => 'button', 'id' => 'ctrl-lock-poll']
    ),
    ['action' => 'lock', $poll->pollid, $adminid],
    ['escape' => false]
);
?>

<!-- DELETE POLL BUTTON -->
<?php echo $this->Form->postLink(
    $this->Form->button(
        __('Delete'), [
        'type' => 'button', 'id' => 'ctrl-delete-poll']
    ),
    ['action' => 'delete', $poll->pollid, $adminid],
    ['escape' => false, 'confirm' => __('Are you sure to delete this poll?')]
);
?>
