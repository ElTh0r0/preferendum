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

class EntriesTable extends Table
{
    public function initialize(array $config): void
    {
        $this->belongsTo('Polls');
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

    public function afterSave(EventInterface $event, $entity, $options)
    {
        // Update timestamp in polls table
        $updatePollTimestamp = $this->Polls->get($entity->poll_id);
        $this->Polls->touch($updatePollTimestamp);
        $this->Polls->save($updatePollTimestamp);
    }
    
    public function afterDelete(EventInterface $event, $entity, $options)
    {
        // Update timestamp in polls table
        $updatePollTimestamp = $this->Polls->get($entity->poll_id);
        $this->Polls->touch($updatePollTimestamp);
        $this->Polls->save($updatePollTimestamp);
    }
}
