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
 * @version   0.6.0
 */
?>

<?php $this->Html->scriptBlock(
    'function showAddChoice() {
    var w = document.getElementById("changechoiceid");
    var x = document.getElementById("divNewChoice");
    var y = document.getElementById("btnAddChoice");
    var z = document.getElementById("choice");
    if (x.style.display === "none") {
        w.value = "";
        x.style.display = "block";
        y.innerText = "-";
        z.value = "";
        z.placeholder = "' . __('New option') . '";
    } else {
        w.value = "";
        x.style.display = "none";
        y.innerText = "+";
        z.placeholder = "' . __('New option') . '";
    }
}

function showEditChoice(currentChoiceId, currentChoiceText) {
    var w = document.getElementById("changechoiceid");
    var x = document.getElementById("divNewChoice");
    var y = document.getElementById("btnAddChoice");
    var z = document.getElementById("choice");
    w.value = currentChoiceId;
    x.style.display = "block";
    y.innerText = "-";
    z.value = currentChoiceText;
    z.placeholder = currentChoiceText;
}',
    ['block' => true]
); ?>

<!-- TABLE HEADER / Swap choices -->
<?php if (count($pollchoices) > 1) {
    echo '<tr>';
    echo '<td class="schedule-blank"></td>';
    for ($i = 0; $i < count($pollchoices); $i++) {
        echo '<td style="text-align: center;">';
        if (0 === $i) {
            echo $this->Form->postLink(
                '>',
                ['controller' => 'Choices', 'action' => 'swap', $poll->id, $adminid, $i, $i + 1]
            );
        } else if ($i === count($pollchoices) - 1) {
            echo $this->Form->postLink(
                '<',
                ['controller' => 'Choices', 'action' => 'swap', $poll->id, $adminid, $i - 1, $i]
            );
        } else {
            echo $this->Form->postLink(
                '<',
                ['controller' => 'Choices', 'action' => 'swap', $poll->id, $adminid, $i - 1, $i]
            ) . '&nbsp;';
            echo $this->Form->postLink(
                '>',
                ['controller' => 'Choices', 'action' => 'swap', $poll->id, $adminid, $i, $i + 1]
            );
        }
        echo '</td>';
    }
    echo '<td></td>';
    echo '</tr>';
} ?>
<!-- DATES -->
<tr>
    <td class="schedule-blank"></td>
    <?php foreach ($pollchoices as $choice) : ?>
        <td class="schedule-header" title="<?php echo h($choice->option) ?>">
            <div>
                <div>
                    <?php echo h($choice->option) ?>
                </div>
            </div>
            <?php
            echo '<button type="button" class="date-edit" onclick="showEditChoice(' . $choice->id . ', \'' . h($choice->option) . '\')"></button>';
            if (sizeof($pollchoices) > 1) {
                echo $this->Form->postLink(
                    $this->Form->button(
                        '',
                        [
                            'type' => 'button', 'class' => 'date-delete'
                        ]
                    ),
                    ['controller' => 'Choices', 'action' => 'delete', $poll->id, $adminid, $choice->id],
                    ['escape' => false, 'confirm' => __('Are you sure to delete option {0}?', h($choice->option))]
                );
            }
            ?>
        </td>
    <?php endforeach; ?>
    <td>
        <?php if (sizeof($pollchoices) < \Cake\Core\Configure::read('preferendum.maxPollOptions')) { ?>
            <button class="schedule-add" id="btnAddChoice" onclick="showAddChoice()">+</button>
            <div id="divNewChoice" style="display: none;">
                <?php
                echo $this->Form->create(
                    $newchoice,
                    [
                        'type' => 'post',
                        'url' => ['controller' => 'Choices', 'action' => 'addedit', $poll->id, $adminid]
                    ]
                );
                echo $this->Form->control(
                    'choice',
                    [
                        'label' => '',
                        'minlength' => '1',
                        'maxlength' => '50',
                        'class' => 'dateInput field-long datepicker-here',
                        'required' => true,
                        'placeholder' => __('New option'),
                    ]
                );
                echo $this->Form->hidden(
                    'id',
                    [
                        'id' => 'changechoiceid',
                        'value' => '',
                    ]
                );
                echo $this->Form->button(__('Save'));
                echo $this->Form->end();
                ?>
            </div>
        <?php } ?>
    </td>
</tr>
