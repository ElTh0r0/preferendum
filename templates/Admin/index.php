<?php
/**
 * PREFERendum (https://github.com/ElTh0r0/preferendum)
 * Copyright (c) github.com/ElTh0r0, github.com/bkis
 *
 * Licensed under The MIT License
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright 2019-2022 github.com/ElTh0r0, github.com/bkis
 * @license   MIT License (https://opensource.org/licenses/mit-license.php)
 * @link      https://github.com/ElTh0r0/preferendum
 * @version   0.4.0
 */
?>
<?php $this->assign('title', __('Poll administration')); ?>

<?php $this->Html->script('poll_admin.js', ['block' => 'scriptBottom']); ?>

<!-- POLL CONTROLS -->
<div id="poll-controls">
    <div>
        <?php echo $this->element('admin/ctrl-logout'); ?>
    </div>
</div>

<div id="admin-page">
    <!-- POLLS OVERVIEW TABLE -->
    <table>
        <tr><td colspan="6"><h1 class="fail"><?php echo __('Available polls') . ': ' . sizeof($polls) ?></h1></td></tr>
        <!-- EXISTING POLLS -->
        <?php
        if (sizeof($polls) > 0) {
            foreach ($polls as $poll) {
                ?>
            <tr>
                <td>
                    <em><?php echo h($poll->title) ?></em><br>
                </td>
                <td><?php echo h($poll->id) ?></td>
                <td>
                    <!-- BTN: VIEW -->
                    <?php echo $this->Html->link(
                        $this->Form->button(__('View'), ['type' => 'button']),
                        ['controller' => 'Polls', 'action' => 'view', $poll->id],
                        ['target' => '_blank', 'escape' => false]
                    ); ?>
                </td>
                <td>
                    <?php echo $this->Html->link(
                        $this->Form->button(__('Edit'), ['type' => 'button']),
                        ['controller' => 'Polls', 'action' => 'edit', $poll->id, $poll->adminid],
                        ['target' => '_blank', 'escape' => false]
                    ); ?>
                </td>
                <td>
                    <?php echo $this->Form->postLink(
                        $this->Form->button(__('Delete'), ['type' => 'button']),
                        ['controller' => 'Polls', 'action' => 'delete', $poll->id, $poll->adminid],
                        ['escape' => false, 'confirm' => __('Are you sure to delete this poll?')]
                    ); ?>
                </td>
                <td>
                    <!-- Info -->
                    <span style="font-size:0.8em;"><?php echo __('Last change') . ': ' . $poll->modified->format('Y-m-d') ?></span>
                </td>
            </tr>
                  <?php
                    if ($poll->userinfo == 1) {
                        if (sizeof($poll->users) > 0) {
                            ?>
                        <tr><td colspan="6">
                            <button type="button" class="collapsible"><?php echo h($poll->title) . ' - ' . __('User contact infos') ?></button>
                            <div class="collapscontent">
                                <ul>
                                <?php foreach ($poll->users as $user) {
                                    echo '<li><em>' . h($user['name']) . ':</em> ' . h($user['info']) . '</li>';
                                } ?>
                                </ul>
                            </div>
                        </td></tr>
                               <?php
                        }
                    }
                    ?>
                <?php
            }
        } else {
            ?>
                <tr>
                    <td height="24" colspan="5"><?php echo __('No polls found in database!') ?></td>
                </tr>
        <?php } ?>

    </table>
</div>
