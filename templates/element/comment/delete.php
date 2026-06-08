<?php

/**
 * PREFERendum
 *
 * SPDX-FileCopyrightText: codeberg.org/ElTh0r0, github.com/bkis
 * SPDX-License-Identifier: MIT
 *
 * @copyright 2019-present codeberg.org/ElTh0r0, github.com/bkis
 * @license   MIT License (https://opensource.org/license/MIT)
 * @link      https://codeberg.org/ElTh0r0/preferendum
 */
?>

<div class="center-box">
    <h1><?php echo __('Delete comments') ?></h1>
    <br>
    <?php
    if (count($poll->comments) > 0) {
        foreach ($poll->comments as $com) {
    ?>
            <div class="comment-container">
                <div class="comment-head">
                    <div class="comment-name"><?php echo h($com->name) ?></div>
                    <div class="comment-date"><?php echo $com->created->format('Y-m-d H:i:s') ?>
                        <?php echo $this->Form->postLink(
                            '',
                            ['controller' => 'Comments', 'action' => 'delete', $poll->id, $adminid, $com->id],
                            [
                                'class' => 'icon-button comment-delete',
                                'confirm' => __('Are you sure to delete comment by {0}?', h($com->name)),
                                'escape' => false,
                            ],
                        ); ?></div>
                </div>
                <div class="comment-text"><?php echo nl2br(h($com->text)) ?></div>
            </div>
    <?php
        }
    } else {
        echo __('No comments, yet.');
    }
    ?>
</div>
