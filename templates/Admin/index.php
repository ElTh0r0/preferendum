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

<?php $this->assign('title', __('Poll administration')); ?>

<div id="control-elements">
    <div>
        <?php echo $this->element('admin/ctrl-logout'); ?>
    </div>
</div>

<div id="admin-page">
    <?php
    echo $this->Flash->render();
    $today = new DateTime();
    $today = $today->format('Y-m-d');

    // ToDo: Replace ugly table layout
    $colsleft = 4; // Default columns: Title, Icons, Votes, Last Change
    if (
        Configure::read('preferendum.alwaysAllowComments') ||
        Configure::read('preferendum.opt_Comments')
    ) {
        $colsleft += 1;
    }
    if (Configure::read('preferendum.opt_PollExpirationAfter') > 0) {
        $colsleft += 1;
    }
    if (Configure::read('preferendum.opt_CollectUserinfo')) {
        $colsleft += 1;
    }

    $colsright = 1; // Default: View button
    // Add buttons for admins
    if (
        strcmp($currentUserRole, $adminRole) == 0 ||
        strcmp($currentUserRole, $polladmRole) == 0
    ) {
        // Export button
        if (Configure::read('preferendum.exportCsv')) {
            $colsright += 1;
        }
        // Edit and deletion buttons
        $colsright += 2;
    }

    $allcols = $colsleft + $colsright;
    ?>

    <!-- POLLS OVERVIEW TABLE -->
    <table style="min-width: 500px">
        <tr>
            <?php echo '<td colspan="3">'; ?>
            <h1 class="fail"><?php echo __('Total polls') . ': ' . $numpolls; ?></h1>
            </td>
            <?php echo '<td colspan="' . ($allcols - 3) . '">'; ?>
            <?php
            if (strcmp($currentUserRole, $adminRole) == 0 || strcmp($currentUserRole, $polladmRole) == 0) {
                $rmInactiveAfter = Configure::read('preferendum.deleteInactivePollsAfter');
                $rmExpiredAfter = Configure::read('preferendum.deleteExpiredPollsAfter');

                if (($rmExpiredAfter > 0) && (Configure::read('preferendum.opt_PollExpirationAfter') > 0)) {
                    echo $this->Html->link(
                        $this->Form->button(__('Expired'), ['type' => 'button', 'class' => 'admin-delexpired-polls']),
                        ['controller' => 'Polls', 'action' => 'cleanupmanually', true],
                        [
                            'escape' => false,
                            'confirm' => __(
                                'Are you sure to delete expired polls (expired since >{0} days)?',
                                $rmExpiredAfter
                            ),
                        ]
                    );
                }
                if ($rmInactiveAfter > 0) {
                    echo $this->Html->link(
                        $this->Form->button(__('Inactive'), ['type' => 'button', 'class' => 'admin-delinactive-polls']),
                        ['controller' => 'Polls', 'action' => 'cleanupmanually', false],
                        [
                            'escape' => false,
                            'confirm' => __(
                                'Are you sure to delete inactive poll (inactive since >{0} days)?',
                                $rmInactiveAfter
                            ),
                        ]
                    );
                }
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
            echo $this->Form->control('search', [
                'label' => '',
                'value' => $this->request->getQuery('search'),
                'id' => 'search_input',
                'placeholder' => __('Search poll or user'),
            ]);
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
                <?php echo __('Filtered polls') . ': ' . $this->Paginator->counter('{{count}}'); ?>
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
        if (count($polls) > 0) {
            $curSortDir = $this->Paginator->param('direction') == 'asc' ? '&uarr;' : '&darr;';
            $sTitle = __('Title');
            $sModi = __('Last change');
            $sExp = __('Expiry date');
            if ($this->Paginator->param('sort') == 'title') {
                $sTitle = '<em>' . $sTitle . ' ' . $curSortDir . '</em>';
            } elseif ($this->Paginator->param('sort') == 'modified') {
                $sModi = '<em>' . $sModi . ' ' . $curSortDir . '</em>';
            } elseif ($this->Paginator->param('sort') == 'expiry') {
                $sExp = '<em>' . $sExp . ' ' . $curSortDir . '</em>';
            }

            // ------ Table header row ------
            // Title
            echo '<tr><td>' . $this->Paginator->sort('title', $sTitle, ['escape' => false]) . '</td>';
            // Icons
            echo '<td></td>';
            // Votes
            echo '<td>' . __('Votes') . '</td>';
            // Comments
            if (
                Configure::read('preferendum.alwaysAllowComments') ||
                Configure::read('preferendum.opt_Comments')
            ) {
                echo '<td>' . __('Comments') . '</td>';
            }
            // Expiry date
            if (Configure::read('preferendum.opt_PollExpirationAfter') > 0) {
                echo '<td>' . $this->Paginator->sort('expiry', $sExp, ['escape' => false]) . '</td>';
            }
            // Last change
            echo '<td>' . $this->Paginator->sort('modified', $sModi, ['escape' => false]) . '</td>';
            // User infos
            if (Configure::read('preferendum.opt_CollectUserinfo')) {
                echo '<td></td>';
            }
            // Buttons
            echo '<td colspan="' . $colsright . '"></td>';
            echo '</tr>';
            // ------ Poll rows ------
            foreach ($polls as $poll) {
        ?>
                <tr>
                    <td>
                        <em><?php echo h($poll->title) ?></em><br>
                    </td>
                    <td>
                        <?php
                        // Icons
                        if (strcmp($poll->adminid, 'NA') == 0) {
                            echo '<img src="img/icon-no-key.png" title="' .
                                __('Poll not protected by admin link') . '"> ';
                        }
                        if ($poll->emailentry || $poll->emailcomment) {
                            if ($poll->emailentry && $poll->emailcomment) {
                                $text = __('Email after new comment/entry');
                            } elseif ($poll->emailentry) {
                                $text = __('Email after new entry');
                            } else {
                                $text = __('Email after new comment');
                            }
                            echo '<img src="img/icon-email.png" title="' . $text . ': ' . $poll->email . '"> ';
                        }
                        if ($poll->userinfo) {
                            echo '<img src="img/icon-user-info.png" title="' . __('Collect user info') . '"> ';
                        }
                        if (
                            Configure::read('preferendum.opt_Comments') &&
                            !$poll->comment &&
                            !Configure::read('preferendum.alwaysAllowComments')
                        ) {
                            echo '<img src="img/icon-no-comment.png" title="' . __('No comments allowed') . '"> ';
                        }
                        if ($poll->hidevotes) {
                            echo '<img src="img/icon-eye-off.png" title="' . __('Poll votes hidden') . '"> ';
                        }
                        if ($poll->editentry) {
                            echo '<img src="img/icon-edit.png" title="' . __('Users can modify their entry') . '"> ';
                        }
                        if ($poll->pwprotect) {
                            echo '<img src="img/icon-password.png" title="' . __('Poll is password protected') . '"> ';
                        }
                        if ($poll->anonymous) {
                            echo '<img src="img/icon-anon.png" title="' . __('Poll is anonymously') . '"> ';
                        }
                        if ($poll->locked) {
                            echo '<img src="img/icon-locked.png" title="' . __('Poll locked') . '"> ';
                        }
                        ?>
                    </td>
                    <td>
                        <?php
                        // Number of votes
                        if (array_key_exists($poll->id, $numentries)) {
                            echo $numentries[$poll->id];
                        } else {
                            echo 0;
                        }
                        ?>
                    </td>
                    <?php
                    // Number of comments
                    if (
                        Configure::read('preferendum.alwaysAllowComments') ||
                        Configure::read('preferendum.opt_Comments')
                    ) {
                        echo '<td>';
                        if (array_key_exists($poll->id, $numcomments)) {
                            echo $numcomments[$poll->id];
                        } else {
                            echo 0;
                        }
                        echo '</td>';
                    }
                    ?>
                    <?php
                    // Expiry date
                    if (Configure::read('preferendum.opt_PollExpirationAfter') > 0) {
                        $exp = '-';
                        $style = '';
                        if ($poll->expiry) {
                            $exp = $poll->expiry->format('Y-m-d');
                            if ($exp < $today) {
                                $style = 'class="fail"';
                            }
                        }
                        echo '<td><span ' . $style . ' style="font-size:0.8em;">' . $exp . '</span></td>';
                    }
                    ?>
                    <td>
                        <span style="font-size:0.8em;"><?php echo $poll->modified->format('Y-m-d') ?></span>
                    </td>
                    <?php
                    // User infos
                    if (Configure::read('preferendum.opt_CollectUserinfo')) {
                        echo '<td>';
                        if ($poll->userinfo) {
                            echo $this->Html->link(
                                $this->Form->button('', ['type' => 'button', 'class' => 'admin-view-userinfo']),
                                ['action' => 'userinfo', $poll->id],
                                ['target' => '_blank', 'escape' => false]
                            );
                        }
                        echo '</td>';
                    }
                    ?>
                    <td>
                        <?php
                        // View button
                        echo $this->Html->link(
                            $this->Form->button('', ['type' => 'button', 'class' => 'admin-view-poll']),
                            ['controller' => 'Polls', 'action' => 'view', $poll->id],
                            ['target' => '_blank', 'escape' => false]
                        );
                        ?>
                    </td>
                    <?php
                    // Edit button
                    if (
                        strcmp($currentUserRole, $adminRole) == 0 ||
                        strcmp($currentUserRole, $polladmRole) == 0
                    ) {
                        echo '<td>';
                        echo $this->Html->link(
                            $this->Form->button('', ['type' => 'button', 'class' => 'admin-edit-poll']),
                            ['controller' => 'Polls', 'action' => 'edit', $poll->id, $poll->adminid],
                            ['target' => '_blank', 'escape' => false]
                        );
                        echo '</td>';
                    } ?>
                    <?php
                    // CSV export button
                    if (
                        Configure::read('preferendum.exportCsv') &&
                        (strcmp($currentUserRole, $adminRole) == 0 ||
                            strcmp($currentUserRole, $polladmRole) == 0)
                    ) {
                        echo '<td>';
                        echo $this->Form->postLink(
                            $this->Form->button('', ['type' => 'button', 'class' => 'admin-export-poll']),
                            ['controller' => 'Polls', 'action' => 'exportcsv', $poll->id, $poll->adminid],
                            ['escape' => false]
                        );
                        echo '</td>';
                    } ?>
                    <?php
                    // Delete button
                    if (
                        strcmp($currentUserRole, $adminRole) == 0 ||
                        strcmp($currentUserRole, $polladmRole) == 0
                    ) {
                        echo '<td>';
                        echo $this->Form->postLink(
                            $this->Form->button('', ['type' => 'button', 'class' => 'admin-delete-poll']),
                            ['controller' => 'Polls', 'action' => 'delete', $poll->id, $poll->adminid],
                            ['escape' => false, 'confirm' => __('Are you sure to delete poll {0}?', h($poll->title))]
                        );
                        echo '</td>';
                    } ?>
                </tr>
            <?php
            }
            // ------ End of poll rows ------

            echo '<tr><td colspan="' . $allcols . '" class="pagination" style="text-align:center"><ul>' .
                $this->Paginator->first('<<') . $this->Paginator->prev('<') . $this->Paginator->numbers() .
                $this->Paginator->next('>') . $this->Paginator->last('>>') . '</ul></td></tr>';
        } else {
            ?>
            <tr>
                <?php echo '<td height="24" colspan="' . $allcols . '">' .
                    __('No polls found in database!') . '</td>'; ?>
            </tr>
        <?php } ?>

    </table>
</div>
