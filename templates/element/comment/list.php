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

use Cake\Core\Configure;
?>

<div class="center-box">
    <h1><?php echo __('Comments') ?></h1>
    <br>
    <?php
    if (count($poll->comments) > 0) {
        foreach ($poll->comments as $com) {
    ?>
            <div class="comment-container">
                <div class="comment-head">
                    <div class="comment-name"><?php echo h($com->name) ?></div>
                    <div class="comment-date">
                        <?php echo $com->created->format(Configure::read('preferendum.dateformatComments')) ?>
                    </div>
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
