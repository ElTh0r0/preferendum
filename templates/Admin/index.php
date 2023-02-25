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
 * @version   0.5.0
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
    <?php echo $this->Flash->render() ?>

    <!-- POLLS OVERVIEW TABLE -->
    <table>
        <tr>
            <td colspan="5">
                <h1 class="fail"><?php echo __('Available polls') . ': ' . $this->Paginator->param('count'); ?></h1>
            </td>
            <td colspan="3">
                <?php
                if (
                    \Cake\Core\Configure::read('preferendum.restrictPollCreation') &&
                    (strcmp($currentUserRole, $adminRole) == 0 || strcmp($currentUserRole, $polladmRole) == 0)
                ) {
                    echo $this->Html->link(
                        $this->Form->button(__('New poll'), ['type' => 'button', 'class' => 'admin-new-poll']),
                        ['controller' => 'Polls', 'action' => 'add'],
                        ['escape' => false]
                    );
                }
                ?>
            </td>
        </tr>
        <!-- EXISTING POLLS -->
        <?php
        if (sizeof($polls) > 0) {
            $curSortDir = ($this->Paginator->sortDir() == 'asc') ? "&uarr;" : "&darr;";
            $sTitle = __('Title');
            $sModi = __('Last change');
            if ($this->Paginator->sortKey() == 'title') {
                $sTitle = '<em>' . $sTitle . ' ' . $curSortDir . '</em>';
            } else if ($this->Paginator->sortKey() == 'modified') {
                $sModi = '<em>' . $sModi . ' ' . $curSortDir . '</em>';
            }
            echo '<tr><td>' . $this->Paginator->sort('title', $sTitle, ['escape' => false]) . '</td>';
            echo '<td></td>';
            echo '<td>' . __('Votes') . '</td>';
            if (
                \Cake\Core\Configure::read('preferendum.alwaysAllowComments')
                || \Cake\Core\Configure::read('preferendum.opt_Comments')
            ) {
                echo '<td>' . __('Comments') . '</td>';
            } else {
                echo '<td></td>';
            }
            echo '<td>' . $this->Paginator->sort('modified', $sModi, ['escape' => false]) . '</td>';
            echo '<td colspan="3"></td></tr>';
            foreach ($polls as $poll) {
        ?>
                <tr>
                    <td>
                        <em><?php echo h($poll->title) ?></em><br>
                    </td>
                    <td><?php if (strcmp($poll->adminid, 'NA') == 0) {
                            echo '<img src="img/icon-no-key.png" title="' . __('Poll not protected by admin link') . '"/> ';
                        }
                        if ($poll->emailentry or $poll->emailcomment) {
                            if ($poll->emailentry and $poll->emailcomment) {
                                $text = __('Email after new comment/entry');
                            } else if ($poll->emailentry) {
                                $text = __('Email after new entry');
                            } else {
                                $text = __('Email after new comment');
                            }
                            echo '<img src="img/icon-email.png" title="' . $text . ': '  . $poll->email . '"/> ';
                        }
                        if ($poll->userinfo) {
                            echo '<img src="img/icon-user-info.png" title="' . __('Collect user info') . '"/> ';
                        }
                        if (
                            \Cake\Core\Configure::read('preferendum.opt_Comments')
                            && !($poll->comment)
                            && !(\Cake\Core\Configure::read('preferendum.alwaysAllowComments'))
                        ) {
                            echo '<img src="img/icon-no-comment.png" title="' . __('No comments allowed') . '"/> ';
                        }
                        if ($poll->hideresult) {
                            echo '<img src="img/icon-eye-off.png" title="' . __('Poll result hidden') . '"/> ';
                        }
                        if ($poll->locked) {
                            echo '<img src="img/icon-locked.png" title="' . __('Poll locked') . '"/> ';
                        }
                        ?></td>
                    <td>
                        <?php
                        if (array_key_exists($poll->id, $numentries)) {
                            echo $numentries[$poll->id];
                        } else {
                            echo 0;
                        }
                        ?>
                    </td>
                    <td>
                        <?php
                        if (
                            \Cake\Core\Configure::read('preferendum.alwaysAllowComments')
                            || \Cake\Core\Configure::read('preferendum.opt_Comments')
                        ) {
                            if (array_key_exists($poll->id, $numcomments)) {
                                echo $numcomments[$poll->id];
                            } else {
                                echo 0;
                            }
                        }
                        ?>
                    </td>
                    <td>
                        <span style="font-size:0.8em;"><?php echo $poll->modified->format('Y-m-d') ?></span>
                    </td>
                    <td>
                        <!-- BTN: VIEW -->
                        <?php echo $this->Html->link(
                            $this->Form->button('', ['type' => 'button', 'class' => 'admin-view-poll']),
                            ['controller' => 'Polls', 'action' => 'view', $poll->id],
                            ['target' => '_blank', 'escape' => false]
                        ); ?>
                    </td>
                    <td>
                        <?php
                        if (
                            strcmp($currentUserRole, $adminRole) == 0 ||
                            strcmp($currentUserRole, $polladmRole) == 0
                        ) {
                            echo $this->Html->link(
                                $this->Form->button('', ['type' => 'button', 'class' => 'admin-edit-poll']),
                                ['controller' => 'Polls', 'action' => 'edit', $poll->id, $poll->adminid],
                                ['target' => '_blank', 'escape' => false]
                            );
                        } ?>
                    </td>
                    <td>
                        <?php
                        if (
                            strcmp($currentUserRole, $adminRole) == 0 ||
                            strcmp($currentUserRole, $polladmRole) == 0
                        ) {
                            echo $this->Form->postLink(
                                $this->Form->button('', ['type' => 'button', 'class' => 'admin-delete-poll']),
                                ['controller' => 'Polls', 'action' => 'delete', $poll->id, $poll->adminid],
                                ['escape' => false, 'confirm' => __('Are you sure to delete this poll?')]
                            );
                        } ?>
                    </td>
                </tr>
                <?php
                if ($poll->userinfo == 1) {
                    if (array_key_exists($poll->id, $userinfos)) {
                        if (sizeof($userinfos[$poll->id]) > 0) {
                ?>
                            <tr>
                                <td colspan="8">
                                    <!-- Info -->
                                    <button type="button" class="collapsible"><?php echo h($poll->title) . ' - ' . __('User contact infos') ?></button>
                                    <div class="collapscontent">
                                        <ul>
                                            <?php foreach ($userinfos[$poll->id] as $user => $info) {
                                                echo '<li><em>' . h($user) . ':</em> ' . h($info) . '</li>';
                                            } ?>
                                        </ul>
                                    </div>
                                </td>
                            </tr>
                <?php
                        }
                    }
                }
                ?>
            <?php
            }
            echo '<tr><td colspan="8" class="pagination" style="text-align:center">' . $this->Paginator->first('<<') . $this->Paginator->prev('<') . $this->Paginator->numbers() . $this->Paginator->next('>') . $this->Paginator->last('>>') . '<td></tr>';
        } else {
            ?>
            <tr>
                <td height="24" colspan="8"><?php echo __('No polls found in database!') ?></td>
            </tr>
        <?php } ?>

    </table>
</div>
