<?php
/**
 * PREFERendum (https://github.com/ElTh0r0/preferendum)
 * Copyright (c) github.com/ElTh0r0
 *
 * Licensed under The MIT License
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright 2019-2022 github.com/ElTh0r0
 * @license   MIT License (https://opensource.org/licenses/mit-license.php)
 * @link      https://github.com/ElTh0r0/preferendum
 * @version   0.4.0
 */
namespace App\Model\Table;

use Cake\ORM\Table;
use Cake\Event\EventInterface;
use ArrayObject;
use Cake\Validation\Validator;

class PollsTable extends Table
{
    public function initialize(array $config): void
    {
        $this->addBehavior(
            'Timestamp', [
            'events' => [
                'Model.beforeSave' => [
                    'modified' => 'always'
                ]
            ]
            ]
        );
        
        $this->hasMany('Choices')->setForeignKey('pollid')->setDependent(true);
        $this->hasMany('Entries')->setForeignKey('pollid')->setDependent(true);
        $this->hasMany('Comments')->setForeignKey('pollid')->setDependent(true);
        $this->hasMany('Users')->setForeignKey('pollid')->setDependent(true);
    }

    public function beforeMarshal(EventInterface $event, ArrayObject $data, ArrayObject $options)
    {
        // Trim all strings before saving
        foreach ($data as $key => $value) {
            if (is_string($value)) {
                $data[$key] = trim($value);
            }
        }
    }

    public function beforeSave(EventInterface $event, $entity, $options)
    {
        if ($entity->isNew() && !$entity->pollid) {
            $entity->pollid = hash("crc32", time() . $entity->title);
            if ($entity->adminLink != true) {
                $entity->adminid = "NA";
            } else {
                $entity->adminid = hash("crc32", time() . $entity->title . "admin");
            }
        }
    }

    public function validationDefault(Validator $validator): Validator
    {
        $validator
            ->notEmptyString('title')
            ->maxLength('title', 255);
        
        $validator
            ->allowEmptyString('details')
            ->maxLength('details', 511);
        
        $validator
            ->notEmptyString('options')
            ->maxLength('options', 32);

        $validator
            ->maxLength('email', 32);
        
        return $validator;
    }
}
