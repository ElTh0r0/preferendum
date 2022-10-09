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
 * @version   0.5.0
 */
?>

<!DOCTYPE html>
<html>
<head>
    <?php echo $this->Html->charset() ?>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>
        <?php echo __('PREFERendum') ?>:
        <?php echo $this->fetch('title') ?>
    </title>
    <meta name="robots" content="noindex,nofollow">
    <?php echo $this->Html->meta('icon') ?>

    <?php echo $this->Html->css(['reset', 'preferendum']) ?>
    <?php echo $this->Html->script('jquery-3.4.1.min.js', array('inline' => false)) ?>

    <?php echo $this->fetch('meta') ?>
    <?php echo $this->fetch('css') ?>
    <?php echo $this->fetch('script') ?>
</head>
<body>
    <noscript>
        <div id="noscript">
            <img src="img/logo.png" alt=""/>
            <span><?php echo __('Please enable JavaScript in your browser and reload this page.') ?></span>
        </div>
    </noscript>

    <!-- BEGIN PAGE HTML -->
    <?php echo $this->element('header'); ?>

    <?php echo $this->Flash->render() ?>
    <?php echo $this->fetch('content') ?>    
    
    <?php echo $this->element('footer'); ?>
    <?php echo $this->fetch('scriptBottom'); ?>
</body>
</html>
