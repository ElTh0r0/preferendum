<?php
/**
 * PREFERendum (https://github.com/ElTh0r0/preferendum)
 * Copyright (c) github.com/ElTh0r0
 *
 * Licensed under The MIT License
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright 2019-present github.com/ElTh0r0
 * @license   MIT License (https://opensource.org/licenses/mit-license.php)
 * @link      https://github.com/ElTh0r0/preferendum
 * @version   0.5.0
 */
declare(strict_types=1);

namespace App\Model\Table;

use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Event\EventInterface;
use ArrayObject;
use Cake\Validation\Validator;

/**
 * Polls Model
 *
 * @property \App\Model\Table\ChoicesTable&\Cake\ORM\Association\HasMany $Choices
 * @property \App\Model\Table\CommentsTable&\Cake\ORM\Association\HasMany $Comments
 *
 * @method \App\Model\Entity\Poll newEmptyEntity()
 * @method \App\Model\Entity\Poll newEntity(array $data, array $options = [])
 * @method \App\Model\Entity\Poll[] newEntities(array $data, array $options = [])
 * @method \App\Model\Entity\Poll get($primaryKey, $options = [])
 * @method \App\Model\Entity\Poll findOrCreate($search, ?callable $callback = null, $options = [])
 * @method \App\Model\Entity\Poll patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method \App\Model\Entity\Poll[] patchEntities(iterable $entities, array $data, array $options = [])
 * @method \App\Model\Entity\Poll|false save(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\Poll saveOrFail(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\Poll[]|\Cake\Datasource\ResultSetInterface|false saveMany(iterable $entities, $options = [])
 * @method \App\Model\Entity\Poll[]|\Cake\Datasource\ResultSetInterface saveManyOrFail(iterable $entities, $options = [])
 * @method \App\Model\Entity\Poll[]|\Cake\Datasource\ResultSetInterface|false deleteMany(iterable $entities, $options = [])
 * @method \App\Model\Entity\Poll[]|\Cake\Datasource\ResultSetInterface deleteManyOrFail(iterable $entities, $options = [])
 *
 * @mixin \Cake\ORM\Behavior\TimestampBehavior
 */
class PollsTable extends Table
{
    public function initialize(array $config): void
    {
        parent::initialize($config);

        $this->setTable('polls');
        $this->setDisplayField('title');
        $this->setPrimaryKey('id');

        $this->addBehavior(
            'Timestamp', [
            'events' => [
                'Model.beforeSave' => [
                    'modified' => 'always'
                ]
            ]
            ]
        );
        
        $this->hasMany('Choices', [
            'foreignKey' => 'poll_id',
        ])->setDependent(true);
        $this->hasMany('Comments', [
            'foreignKey' => 'poll_id',
        ])->setDependent(true);
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
        if ($entity->isNew() && !$entity->id) {
            $entity->id = hash("crc32", time() . $entity->title);
            if ($entity->adminid != true) {
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
            ->maxLength('email', 32);
        
        return $validator;
    }
}
