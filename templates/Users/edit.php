<?php

/**
 * PREFERendum (https://github.com/ElTh0r0/preferendum)
 * Copyright (c) github.com/ElTh0r0
 *
 * Licensed under The MIT License
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright 2020-present github.com/ElTh0r0
 * @license   MIT License (https://opensource.org/licenses/mit-license.php)
 * @link      https://github.com/ElTh0r0/preferendum
 * @version   0.8.0
 */
?>

<div id="control-elements">
    <div>
        <?php echo $this->element('admin/ctrl-logout'); ?>
    </div>
</div>

<div id="useradmin-page">
    <?php
    echo $this->Flash->render();

    if (strcmp($currentUserRole, $allroles[0]) == 0) {
        echo $this->element('user/list');
        echo '<br />';
        echo '<hr />';

        echo $this->Form->postLink(
            '&larr; ' . __('Back to user creation'),
            ['action' => 'management'],
            ['escape' => false]
        );
    }

    echo $this->element('user/edit-form');
    ?>
</div>
