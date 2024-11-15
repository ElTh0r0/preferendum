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
 * @version   0.8.0
 */

declare(strict_types=1);

namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * Poll Entity
 *
 * @property string $id
 * @property string $adminid
 * @property string $title
 * @property string|null $details
 * @property string|null $email
 * @property bool $emailentry
 * @property bool $emailcomment
 * @property bool $userinfo
 * @property bool $hidevotes
 * @property bool $anonymous
 * @property bool $locked
 * @property bool $expiry
 * @property bool $modified
 * @property bool $pwprotect
 * @property bool $limitentry
 *
 * @property \Cake\I18n\DateTime $modified
 *
 * @property \App\Model\Entity\Choice[] $choices
 * @property \App\Model\Entity\Comment[] $comments
 */
class Poll extends Entity
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
        'adminid' => true,
        'title' => true,
        'details' => true,
        'email' => true,
        'emailentry' => true,
        'emailcomment' => true,
        'emailpoll' => true, // Not stored in DB, but send from form to Controller
        'userinfo' => true,
        'editentry' => true,
        'comment' => true,
        'hidevotes' => true,
        'anonymous' => true,
        'locked' => true,
        'hasexp' => true, // Not stored in DB, but send from form to Controller
        'expiry' => true,
        'modified' => true,
        'choices' => true,
        'comments' => true,
        'pwprotect' => true,
        'limitentry' => true,
        'max_entries' => true,
    ];
}
