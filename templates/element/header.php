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
 */

use Cake\Core\Configure;
?>

<div id="header" class="right-to-left">

    <div id="info">
        <?php
        $request = $this->request->getUri()->getPath();
        if ((str_contains($request, '/admin') || str_contains($request, '/users')) && !isset($pollid)) { ?>
            <h1><?php echo __('Administration') ?></h1>
            <p class="details"></p>
        <?php } elseif (!isset($poll) || $poll->isNew() || isset($pollid)) { ?>
            <h1><?php echo __('PREFERendum') ?></h1>
            <p class="details"><?php echo __('scheduling polls') ?></p>
        <?php } else { ?>
            <h1><?php echo h($poll->title) ?></h1>
            <p class="details"><?php echo h($poll->details) ?></p>

            <?php if (stripos($request, 'polls/edit') > 0) { ?>
                <div class="poll-url-container">
                    <span class="success"><em><?php echo __('Public link') . ':' ?></em></span>
                    <input type="text" id="public-url-field" title="<?php echo __('Give this public link to the participants of your poll!') ?>" readonly>
                    <button type="button" class="copy-trigger" data-clipboard-target="#public-url-field" title="<?php echo __('Copy link to clipboard!') ?>"></button>
                    <span class="pale">&nbsp;&larr; <?php echo __('Give this public link to the participants of your poll!') ?></span>
                </div>
            <?php } else { ?>
                <div class="poll-url-container">
                    <span class="success"><em><?php echo __('Public link') . ':' ?></em></span>
                    <input type="text" id="public-url-field" readonly>
                    <button type="button" class="copy-trigger" data-clipboard-target="#public-url-field" title="<?php echo __('Copy link to clipboard!') ?>"></button>
                </div>
            <?php } ?>
            <?php if (strcmp($poll->adminid, 'NA') != 0 && strcmp($poll->adminid, $adminid) == 0) { ?>
                <div class="poll-url-container">
                    <span class="fail"><em><?php echo __('Admin link') . ':' ?></em></span>
                    <input type="text" id="admin-url-field" title="<?php echo __('Save this admin link, you need it to manage your poll!') ?>" readonly>
                    <button type="button" class="copy-trigger" data-clipboard-target="#admin-url-field" title="<?php echo __('Copy link to clipboard!') ?>"></button>
                    <span class="pale">&nbsp;&larr; <?php echo __('Save this admin link, you need it to manage your poll!') ?></span>
                </div>
            <?php } ?>
        <?php } ?>

    </div>

    <?php if (Configure::read('preferendum.headerLogo')) { ?>
        <div id="logo">
            <a href="<?php echo $this->request->getAttributes()['webroot'] ?>" title="<?php echo __('Create a new poll ...') ?>">
                <img src=<?php echo $this->request->getAttributes()['webroot'] . 'img/logo.png' ?> alt="">
            </a>
        </div>
    <?php } ?>

</div>
