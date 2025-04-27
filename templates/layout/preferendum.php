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

<!DOCTYPE html>
<html lang="<?php echo str_replace('_', '-', Configure::read('App.defaultLocale')) ?>" data-theme="light">

<head>
    <?php echo $this->Html->charset() ?>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>
        <?php echo __('PREFERendum') ?>:
        <?php echo $this->fetch('title') ?>
    </title>
    <meta name="robots" content="noindex,nofollow">

    <?php
    echo $this->Html->meta('icon', '/favicon-48x48.png', ['type' => 'image/png', 'sizes' => '48x48']);
    echo $this->Html->meta('icon', '/favicon-32x32.png', ['type' => 'image/png', 'sizes' => '32x32']);
    echo $this->Html->meta('icon', '/favicon-16x16.png', ['type' => 'image/png', 'sizes' => '16x16']);
    echo $this->Html->meta('icon', '/favicon.ico', ['type' => 'image/x-icon']);

    echo $this->Html->css(['reset', 'preferendum']);
    echo $this->Html->script('jquery-3.6.3.min.js', ['inline' => false]);
    if (Configure::read('preferendum.toggleTheme')) {
        echo $this->Html->script('theme_toggle.js', ['block' => 'scriptBottom']);
    }

    echo $this->fetch('meta');
    echo $this->fetch('css');
    echo $this->fetch('script');
    ?>
</head>

<body>
    <noscript>
        <div id="noscript">
            <img src="img/logo.svg" alt="">
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
