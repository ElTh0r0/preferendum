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
 * @version   0.7.1
 */

declare(strict_types=1);

namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * Choice Entity
 *
 * @property int $id
 * @property string $poll_id
 * @property string $option
 * @property int $max_entries
 * @property int $sort
 *
 * @property \App\Model\Entity\Poll $poll
 * @property \App\Model\Entity\Entry[] $entries
 */
class Choice extends Entity
{
    /**
     * Fields that can be mass assigned using newEntity() or patchEntity().
     *
     * Note that when '*' is set to true, this allows all unspecified fields to
     * be mass assigned. For security purposes, it is advised to set '*' to false
     * (or remove it), and explicitly make individual fields accessible as needed.
     *
     * @var array<string, bool>
     */
    protected $_accessible = [
        'poll_id' => true,
        'option' => true,
        'max_entries' => true,
        'sort' => true,
        'poll' => true,
        'entries' => true,
    ];
}
