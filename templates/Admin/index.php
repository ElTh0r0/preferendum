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
    <?php
    echo $this->Flash->render();
    $today = new DateTime();

    // ToDo: Replace ugly table layout
    $allcols = 8;
    $colsleft = 5;
    $colsright = 3;
    if (\Cake\Core\Configure::read('preferendum.opt_PollExpirationAfter') > 0) {
        $allcols += 1;
        $colsleft += 1;
    }
    ?>

    <!-- POLLS OVERVIEW TABLE -->
    <table style="min-width: 500px">
        <tr>
            <?php echo '<td colspan="' . $colsleft . '">'; ?>
            <h1 class="fail"><?php echo __('Total polls') . ': ' . $numpolls; ?></h1>
            </td>
            <?php echo '<td colspan="' . $colsright . '">'; ?>
            <?php
            if (strcmp($currentUserRole, $adminRole) == 0 || strcmp($currentUserRole, $polladmRole) == 0) {
                echo $this->Html->link(
                    $this->Form->button(__('New poll'), ['type' => 'button', 'class' => 'admin-new-poll']),
                    ['controller' => 'Polls', 'action' => 'add'],
                    ['escape' => false]
                );
            }
            ?>
            </td>
        </tr>
        <tr>
            <?php echo '<td colspan="' . $allcols . '">'; ?>
            <?php
            echo $this->Form->create(null, ['type' => 'get', 'id' => 'search_form']);
            echo $this->Form->control('search', ['label' => '', 'value' => $this->request->getQuery('search'), 'id' => 'search_input', 'placeholder' => __('Search poll or user'),]);
            echo $this->Html->link(
                $this->Form->button(__('Clear filter'), ['type' => 'button', 'id' => 'search_clear']),
                ['controller' => 'admin', 'action' => 'index'],
                ['escape' => false]
            );
            echo $this->Form->submit(__('Search'), ['id' => 'search_submit']);
            echo $this->Form->end();
            ?>
            </td>
        </tr>
        <?php if ($this->request->getQuery('search')) { ?>
            <tr>
                <?php echo '<td colspan="' . $allcols . '">'; ?>
                <?php echo __('Filtered polls') . ': ' . $this->Paginator->param('count'); ?>
                </td>
            </tr>
        <?php } ?>
        <tr>
            <?php echo '<td colspan="' . $allcols . '">'; ?>
            <?php echo '&nbsp;' ?>
            </td>
        </tr>
        <!-- EXISTING POLLS -->
        <?php
        if (sizeof($polls) > 0) {
            $curSortDir = ($this->Paginator->sortDir() == 'asc') ? "&uarr;" : "&darr;";
            $sTitle = __('Title');
            $sModi = __('Last change');
            $sExp = __('Expiry date');
            if ($this->Paginator->sortKey() == 'title') {
                $sTitle = '<em>' . $sTitle . ' ' . $curSortDir . '</em>';
            } else if ($this->Paginator->sortKey() == 'modified') {
                $sModi = '<em>' . $sModi . ' ' . $curSortDir . '</em>';
            } else if ($this->Paginator->sortKey() == 'expiry') {
                $sExp = '<em>' . $sExp . ' ' . $curSortDir . '</em>';
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
            if (\Cake\Core\Configure::read('preferendum.opt_PollExpirationAfter') > 0) {
                echo '<td>' . $this->Paginator->sort('expiry', $sExp, ['escape' => false]) . '</td>';
            }
            echo '<td>' . $this->Paginator->sort('modified', $sModi, ['escape' => false]) . '</td>';
            echo '<td colspan="' . $colsright . '"></td>';
            echo '</tr>';
            foreach ($polls as $poll) {
        ?>
                <tr>
                    <td>
                        <em><?php echo h($poll->title) ?></em><br>
                    </td>
                    <td>
                        <?php
                        if (strcmp($poll->adminid, 'NA') == 0) {
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
                        if ($poll->hidevotes) {
                            echo '<img src="img/icon-eye-off.png" title="' . __('Poll votes hidden') . '"/> ';
                        }
                        if ($poll->editentry) {
                            echo '<img src="img/icon-edit.png" title="' . __('Users can modify their entry') . '"/> ';
                        }
                        if ($poll->pwprotect) {
                            echo '<img src="img/icon-password.png" title="' . __('Poll is password protected') . '"/> ';
                        }
                        if ($poll->locked) {
                            echo '<img src="img/icon-locked.png" title="' . __('Poll locked') . '"/> ';
                        }
                        ?>
                    </td>
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
                    <?php if (\Cake\Core\Configure::read('preferendum.opt_PollExpirationAfter') > 0) {
                        $exp = '-';
                        $style = '';
                        if ($poll->expiry) {
                            if ($poll->expiry < $today) {
                                $style = 'class="fail"';
                            }
                            $exp = $poll->expiry->format('Y-m-d');
                        }
                        echo '<td><span ' . $style . ' style="font-size:0.8em;">' . $exp . '</span></td>';
                    } ?>
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
                                <?php echo '<td colspan="' . $allcols . '">'; ?>
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
            echo '<tr><td colspan="' . $allcols . '" class="pagination" style="text-align:center">' . $this->Paginator->first('<<') . $this->Paginator->prev('<') . $this->Paginator->numbers() . $this->Paginator->next('>') . $this->Paginator->last('>>') . '<td></tr>';
        } else {
            ?>
            <tr>
                <?php echo '<td height="24" colspan="' . $allcols . '">' . __('No polls found in database!') . '</td>'; ?>
            </tr>
        <?php } ?>

    </table>
</div>
