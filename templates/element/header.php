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

<div id="header" class="right-to-left">

    <div id="info">
        <!-- HEADER IS USED IN OTHER VIEW -->
        <!-- On admin page $poll==Null / not set -->
        <?php
        $base = $this->request->getUri()->getPath();
        $base = basename($base);
        if (strcmp($base, 'admin') == 0 || strcmp($base, 'login') == 0 || strcmp($base, 'usermanagement') == 0) { ?>
            <h1><?php echo __('Poll administration') ?></h1>
            <p class="details"></p>
        <?php } elseif (!isset($poll)) { ?>
            <h1><?php echo __('sprudel-ng') ?></h1>
            <p class="details"><?php echo __('scheduling polls') ?></p>
        <?php } elseif ($poll->isNew()) { ?>
            <h1><?php echo __('sprudel-ng') ?></h1>
            <p class="details"><?php echo __('scheduling polls') ?></p>
        <!-- HEADER IS USED IN POLL VIEW -->
        <?php } else { ?>
            <h1><?php echo h($poll->title) ?></h1>
            <p class="details"><?php echo h($poll->details) ?></p>
            
            <div class="poll-url-container">
                <span class="success"><em><?php echo __('Public link') . ':' ?></em></span>
                <input type="text" id="public-url-field" title="<?php echo __('Give this public link to the participants of your poll!') ?>" readonly/>
                <button type="button" class="copy-trigger" data-clipboard-target="#public-url-field" title="<?php echo __('Copy link to clipboard!') ?>"></button>
                <span class="pale">&nbsp;&larr; <?php echo __('Give this public link to the participants of your poll!') ?></span>
            </div>
            <?php if (strcmp($poll->adminid, "NA") != 0 && strcmp($poll->adminid, $adminid) == 0) { ?>
            <div class="poll-url-container">
                <span class="fail"><em><?php echo __('Admin link') . ':' ?></em></span>
                <input type="text" id="admin-url-field" title="<?php echo __('Save this admin link, you need it to manage your poll!') ?>" readonly/>
                <button type="button" class="copy-trigger" data-clipboard-target="#admin-url-field" title="<?php echo __('Copy link to clipboard!') ?>"></button>
                <span class="pale">&nbsp;&larr; <?php echo __('Save this admin link, you need it to manage your poll!') ?></span>
            </div>
            <?php } ?>
        <?php } ?>

    </div>

    <?php if (\Cake\Core\Configure::read('Sprudel-ng.headerLogo')) { ?>
    <div id="logo">
        <a href="<?php echo $this->request->getAttributes()['webroot'] ?>" title="<?php echo __('Create a new poll ...') ?>">
            <img src=<?php echo $this->request->getAttributes()['webroot'] . 'img/logo.png' ?> alt=""/>
        </a>
    </div>
    <?php } ?>

</div>
