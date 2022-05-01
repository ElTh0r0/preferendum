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
 * @version   0.4.0
 */
?>

<!-- TABLE HEADER / DATES -->
<tr>
    <td class="schedule-blank"></td>
    <?php foreach ($poll->choices as $choice): ?>
        <td class="schedule-header">
            <div>
                <div>
                    <?php echo h($choice->option) ?>
                </div>
            </div>
            <?php if (sizeof($poll->choices) > 2) {
                echo $this->Form->postLink(
                    $this->Form->button(
                        '', [
                        'type' => 'button', 'class' => 'date-delete']
                    ),
                    ['controller' => 'Choices', 'action' => 'delete', $poll->pollid, $adminid, $choice->option],
                    ['escape' => false, 'confirm' => __('Are you sure to delete this option?')]
                );
            } ?>
        </td>
    <?php endforeach; ?>
    <td><?php
        echo $this->Form->create(
            $option, [
            'type' => 'post',
            'url' => ['controller' => 'Choices', 'action' => 'add', $poll->pollid, $adminid]
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

<!-- EXISTING ENTRIES -->
<?php
foreach ($pollentries as $name => $entry) {
    echo '<tr class="valign-middle">';
    echo '<td class="schedule-names">' . h($name) . '</td>';

    for ($i = 0; $i < sizeof($poll->choices); $i++) {
        $value = 'maybe';
        switch ($entry[$poll->choices[$i]->option]) {
        case 0: $value = 'no'; 
            break;
        case 1: $value = 'yes'; 
            break;
        case 2: $value = 'maybe'; 
            break;
        }

        echo '<td class="schedule-entry schedule-entry-' . $value . '"></td>';
    }

    echo '<td>';
    echo $this->Form->postLink(
        $this->Form->button(
            '', [
                'type' => 'button', 'class' => 'schedule-delete']
        ),
        ['controller' => 'Entries', 'action' => 'delete', $poll->pollid, $adminid, $name],
        ['escape' => false, 'confirm' => __('Are you sure to delete this entry?')]
    );
    echo '</td>';
        
    echo '</tr>';
}
?>
