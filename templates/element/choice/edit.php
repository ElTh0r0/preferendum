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

<?php $this->Html->scriptBlock(
    'function showAddChoice() {
    var w = document.getElementById("changechoiceid");
    var x = document.getElementById("divNewChoice");
    var y = document.getElementById("btnAddChoice");
    var z = document.getElementById("choice");
    var maxInp = document.getElementById("max-entries");
    if (x.style.display === "none") {
        w.value = "";
        x.style.display = "block";
        y.innerText = "-";
        z.value = "";
        z.placeholder = "' . __('New option') . '";
        z.required = true;
    } else {
        w.value = "";
        x.style.display = "none";
        y.innerText = "+";
        z.placeholder = "' . __('New option') . '";
        z.required = false;
        if (maxInp) {
            maxInp.value = "0";
        }
    }
}

function showEditChoice(currentChoiceId, currentChoiceText, currentChoiceMax) {
    var w = document.getElementById("changechoiceid");
    var x = document.getElementById("divNewChoice");
    var y = document.getElementById("btnAddChoice");
    var z = document.getElementById("choice");
    var maxInp = document.getElementById("max-entries");
    w.value = currentChoiceId;
    x.style.display = "block";
    y.innerText = "-";
    z.value = currentChoiceText;
    z.placeholder = currentChoiceText;
    if (maxInp) {
        maxInp.value = currentChoiceMax;
    }
}',
    ['block' => true],
); ?>

<!-- TABLE HEADER / Swap choices -->
<?php
$numChoices = count($pollchoices);
if ($numChoices > 1) {
    echo '<tr>';
    echo '<td class="schedule-blank"></td>';
    for ($i = 0; $i < $numChoices; $i++) {
        echo '<td style="text-align: center;">';
        if ($i === 0) {
            echo $this->Form->postLink(
                '>',
                ['controller' => 'Choices', 'action' => 'swap', $poll->id, $adminid, $i, $i + 1],
            );
        } elseif ($i === $numChoices - 1) {
            echo $this->Form->postLink(
                '<',
                ['controller' => 'Choices', 'action' => 'swap', $poll->id, $adminid, $i - 1, $i],
            );
        } else {
            echo $this->Form->postLink(
                '<',
                ['controller' => 'Choices', 'action' => 'swap', $poll->id, $adminid, $i - 1, $i],
            ) . '&nbsp;';
            echo $this->Form->postLink(
                '>',
                ['controller' => 'Choices', 'action' => 'swap', $poll->id, $adminid, $i, $i + 1],
            );
        }
        echo '</td>';
    }
    echo '<td class="schedule-blank"></td>';
    echo '</tr>';
} ?>
<!-- DATES -->
<tr>
    <td class="schedule-blank"></td>
    <?php foreach ($pollchoices as $choice) : ?>
        <td class="schedule-header" title="
        <?php
        echo h($choice->option);
        if ($poll->limitentry && $choice->max_entries > 0) {
            echo __(' - {0} pers.', $choice->max_entries);
        }
        ?>">
            <div>
                <div>
                    <?php echo h($choice->option) ?>
                    <?php
                    if ($poll->limitentry && $choice->max_entries > 0) {
                        echo __(' - {0} pers.', $choice->max_entries);
                    }
                    ?>
                </div>
            </div>
            <?php
            echo '<button type="button" class="date-edit" onclick="showEditChoice(' .
                $choice->id . ', \'' . h($choice->option) . '\', ' . $choice->max_entries . ')"></button>';
            if ($numChoices > 1) {
                echo $this->Form->postLink(
                    '',
                    ['controller' => 'Choices', 'action' => 'delete', $poll->id, $adminid, $choice->id],
                    [
                        'class' => 'icon-button date-delete',
                        'confirm' => __('Are you sure to delete option {0}?', h($choice->option)),
                        'escape' => false,
                    ],
                );
            }
            ?>
        </td>
    <?php endforeach; ?>
    <td>
        <?php if ($numChoices < Configure::read('preferendum.maxPollOptions')) { ?>
            <button class="schedule-add" id="btnAddChoice" onclick="showAddChoice()">+</button>
            <div id="divNewChoice" style="display: none;">
                <?php
                echo $this->Form->create(
                    $newchoice,
                    [
                        'type' => 'post',
                        'url' => ['controller' => 'Choices', 'action' => 'addedit', $poll->id, $adminid],
                    ],
                );
                echo $this->Form->control(
                    'choice',
                    [
                        'label' => '',
                        'minlength' => '1',
                        'maxlength' => '50',
                        'class' => 'dateInput field-long datepicker-here',
                        'placeholder' => __('New option'),
                    ],
                );
                if ($poll->limitentry) {
                    echo $this->Form->control(
                        'max_entries',
                        [
                            'class' => 'maxEntryInput',
                            'label' => false,
                            'style' => 'margin-top: 3px; height: 25px; width: 50px;',
                            'type' => 'number',
                            'value' => '0',
                            'min' => 0,
                            'max' => 99,
                        ],
                    );
                }
                echo $this->Form->hidden(
                    'id',
                    [
                        'id' => 'changechoiceid',
                        'value' => '',
                    ],
                );
                echo $this->Form->button(__('Save'));
                echo $this->Form->end();
                ?>
            </div>
        <?php } ?>
    </td>
</tr>
