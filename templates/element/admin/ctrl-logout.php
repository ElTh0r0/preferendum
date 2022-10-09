<?php
/**
 * PREFERendum (https://github.com/ElTh0r0/preferendum)
 * Copyright (c) github.com/ElTh0r0
 *
 * Licensed under The MIT License
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright 2020-2022 github.com/ElTh0r0
 * @license   MIT License (https://opensource.org/licenses/mit-license.php)
 * @link      https://github.com/ElTh0r0/preferendum
 * @version   0.5.0
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
                'type' => 'button',
                'id' => 'ctrl-polls',]
            ),
            ['action' => 'index'],
            ['escape' => false]
        );
    } else {
        echo $this->Html->link(
            $this->Form->button(
                __('User management'), [
                'type' => 'button',
                'id' => 'ctrl-usermanagement',]
            ),
            ['action' => 'usermanagement'],
            ['escape' => false]
        );
    }
    echo $this->Form->postLink(
        $this->Form->button(
            __('Logout'), [
            'type' => 'button',
            'id' => 'ctrl-logout',]
        ),
        ['action' => 'logout'],
        ['escape' => false]
    ); ?>
</div>
