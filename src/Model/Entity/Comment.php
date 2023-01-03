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
declare(strict_types=1);

namespace App\Model\Entity;

use Cake\ORM\Entity;


/**
 * Comment Entity
 *
 * @property int $id
 * @property string $poll_id
 * @property string $text
 * @property string $name
 * @property \Cake\I18n\FrozenTime $created
 *
 * @property \App\Model\Entity\Poll $poll
 */
class Comment extends Entity
{
    /**
     * Fields that can be mass assigned using newEntity() or patchEntity().
     */
    protected $_accessible = [
        '*' => true,
        'id' => false,
    ];
}
