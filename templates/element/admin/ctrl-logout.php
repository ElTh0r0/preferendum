<?php
/**
 * Sprudel-ng (https://github.com/ElTh0r0/sprudel-ng)
 * Copyright (c) github.com/ElTh0r0
 *
 * Licensed under The MIT License
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright 2020 github.com/ElTh0r0
 * @license   MIT License (https://opensource.org/licenses/mit-license.php)
 * @link      https://github.com/ElTh0r0/sprudel-ng
 * @since     0.1.0
 */
?>

<div>
    <?php
    $base = $this->request->getUri()->getPath();
    $base = basename($base);
    if (strcmp($base, 'usermanagement') == 0) {
        echo $this->Html->link(
            $this->Form->button(
                __('Poll administration'), [
                'type' => 'button',]
            ),
            ['action' => 'index'],
            ['escape' => false]
        );
    } else {
        echo $this->Html->link(
            $this->Form->button(
                __('User management'), [
                'type' => 'button',]
            ),
            ['action' => 'usermanagement'],
            ['escape' => false]
        );
    }
    echo $this->Form->postLink(
        $this->Form->button(
            __('Logout'), [
            'type' => 'button',]
        ),
        ['action' => 'logout'],
        ['escape' => false]
    ); ?>
</div>
