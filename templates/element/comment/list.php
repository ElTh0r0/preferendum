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
?>

<div class="center-box">
    <h1><?php echo __('Comments') ?></h1>
    <br>
    <?php
    if (sizeof($poll->comments) > 0) {
        foreach ($poll->comments as $com) {
    ?>
            <div class="comment-container">
                <span class="comment-name"><?php echo h($com->name) ?></span>
                <div class="comment-date">
                    <?php echo $com->created->format(\Cake\Core\Configure::read('preferendum.dateformatComments')) ?>
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
