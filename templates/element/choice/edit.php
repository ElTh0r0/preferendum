<?php
/**
 * PREFERendum (https://github.com/ElTh0r0/preferendum)
 * Copyright (c) github.com/ElTh0r0, github.com/bkis
 *
 * Licensed under The MIT License
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright 2019-2023 github.com/ElTh0r0, github.com/bkis
 * @license   MIT License (https://opensource.org/licenses/mit-license.php)
 * @link      https://github.com/ElTh0r0/preferendum
 * @version   0.5.0
 */
?>

<!-- TABLE HEADER / DATES -->
<tr>
    <td class="schedule-blank"></td>
    <?php foreach ($pollchoices as $choice): ?>
        <td class="schedule-header">
            <div>
                <div>
                    <?php echo h($choice->option) ?>
                </div>
            </div>
            <?php if (sizeof($pollchoices) > 2) {
                echo $this->Form->postLink(
                    $this->Form->button(
                        '', [
                        'type' => 'button', 'class' => 'date-delete']
                    ),
                    ['controller' => 'Choices', 'action' => 'delete', $poll->id, $adminid, $choice->id],
                    ['escape' => false, 'confirm' => __('Are you sure to delete this option?')]
                );
            } ?>
        </td>
    <?php endforeach; ?>
    <td><?php
        echo $this->Form->create(
            $option, [
            'type' => 'post',
            'url' => ['controller' => 'Choices', 'action' => 'add', $poll->id, $adminid]
            ]
        );
        echo $this->Form->control(
            'choice', [
            'label' => '',
            'minlength' => '1',
            'maxlength' => '32'
            ]
        );
        echo $this->Form->button('+');
        echo $this->Form->end();
        ?></td>
</tr>
