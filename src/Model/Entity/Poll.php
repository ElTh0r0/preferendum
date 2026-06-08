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
