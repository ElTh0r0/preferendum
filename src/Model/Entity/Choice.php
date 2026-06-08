<?php

/**
 * PREFERendum
 *
 * SPDX-FileCopyrightText: codeberg.org/ElTh0r0, github.com/bkis
 * SPDX-License-Identifier: MIT
 *
 * @copyright 2019-present codeberg.org/ElTh0r0, github.com/bkis
 * @license   MIT License (https://opensource.org/license/MIT)
 * @link      https://codeberg.org/ElTh0r0/preferendum
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
    protected array $_accessible = [
        'poll_id' => true,
        'option' => true,
        'max_entries' => true,
        'sort' => true,
        'poll' => true,
        'entries' => true,
    ];
}
