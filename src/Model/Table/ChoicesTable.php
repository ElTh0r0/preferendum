<?php

/**
 * PREFERendum (https://github.com/ElTh0r0/preferendum)
 * Copyright (c) github.com/ElTh0r0
 *
 * Licensed under The MIT License
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright 2020-present github.com/ElTh0r0
 * @license   MIT License (https://opensource.org/licenses/mit-license.php)
 * @link      https://github.com/ElTh0r0/preferendum
 */

declare(strict_types=1);

namespace App\Model\Table;

use ArrayObject;
use Cake\Datasource\EntityInterface;
use Cake\Event\EventInterface;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * Choices Model
 *
 * @property \App\Model\Table\PollsTable&\Cake\ORM\Association\BelongsTo $Polls
 * @method \App\Model\Entity\Choice newEmptyEntity()
 * @method \App\Model\Entity\Choice newEntity(array $data, array $options = [])
 * @method \App\Model\Entity\Choice[] newEntities(array $data, array $options = [])
 * @method \App\Model\Entity\Choice get($primaryKey, $options = [])
 * @method \App\Model\Entity\Choice findOrCreate($search, ?callable $callback = null, $options = [])
 * @method \App\Model\Entity\Choice patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method \App\Model\Entity\Choice[] patchEntities(iterable $entities, array $data, array $options = [])
 * @method \App\Model\Entity\Choice|false save(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\Choice saveOrFail(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\Choice[]|\Cake\Datasource\ResultSetInterface|false saveMany(iterable $entities, $options = [])
 * @method \App\Model\Entity\Choice[]|\Cake\Datasource\ResultSetInterface saveManyOrFail(iterable $entities, $options = [])
 * @method \App\Model\Entity\Choice[]|\Cake\Datasource\ResultSetInterface|false deleteMany(iterable $entities, $options = [])
 * @method \App\Model\Entity\Choice[]|\Cake\Datasource\ResultSetInterface deleteManyOrFail(iterable $entities, $options = [])
 */
class ChoicesTable extends Table
{
    public function initialize(array $config): void
    {
        parent::initialize($config);

        $this->setTable('choices');
        $this->setDisplayField('id');
        $this->setPrimaryKey('id');

        $this->belongsTo('Polls', [
            'foreignKey' => 'poll_id',
            'joinType' => 'INNER',
        ]);
        $this->hasMany('Entries', [
            'foreignKey' => 'choice_id',
        ])->setDependent(true);
    }

    public function beforeMarshal(EventInterface $event, ArrayObject $data, ArrayObject $options): void
    {
        // Trim all strings before saving
        foreach ($data as $key => $value) {
            if (is_string($value)) {
                $data[$key] = trim($value);
            }
        }
    }

    public function afterSave(EventInterface $event, EntityInterface $entity, ArrayObject $options): void
    {
        // Update timestamp in polls table
        $updatePollTimestamp = $this->Polls->get($entity->poll_id);
        $this->Polls->touch($updatePollTimestamp);
        $this->Polls->save($updatePollTimestamp);
    }

    public function afterDelete(EventInterface $event, EntityInterface $entity, ArrayObject $options): void
    {
        // Update timestamp in polls table
        $updatePollTimestamp = $this->Polls->get($entity->poll_id);
        $this->Polls->touch($updatePollTimestamp);
        $this->Polls->save($updatePollTimestamp);
    }

    /**
     * Default validation rules.
     *
     * @param \Cake\Validation\Validator $validator Validator instance.
     * @return \Cake\Validation\Validator
     */
    public function validationDefault(Validator $validator): Validator
    {
        $validator
            ->scalar('poll_id')
            ->maxLength('poll_id', 32)
            ->notEmptyString('poll_id');

        $validator
            ->scalar('option')
            ->maxLength('option', 50)
            ->requirePresence('option', 'create')
            ->notEmptyString('option');

        $validator
            ->requirePresence('sort', 'create')
            ->notEmptyString('sort');

        return $validator;
    }

    /**
     * Returns a rules checker object that will be used for validating
     * application integrity.
     *
     * @param \Cake\ORM\RulesChecker $rules The rules object to be modified.
     * @return \Cake\ORM\RulesChecker
     */
    public function buildRules(RulesChecker $rules): RulesChecker
    {
        $rules->add($rules->existsIn('poll_id', 'Polls'), ['errorField' => 'poll_id']);

        return $rules;
    }
}
