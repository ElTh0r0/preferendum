<?php
/**
 * PREFERendum (https://github.com/ElTh0r0/preferendum)
 * Copyright (c) github.com/ElTh0r0
 *
 * Licensed under The MIT License
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright 2020-2023 github.com/ElTh0r0
 * @license   MIT License (https://opensource.org/licenses/mit-license.php)
 * @link      https://github.com/ElTh0r0/preferendum
 * @version   0.5.0
 */
declare(strict_types=1);

namespace App\Controller;

class ChoicesController extends AppController
{
    public function add($pollid = null, $adminid = null)
    {
        if ($this->request->is('post')) {
            $data = $this->request->getData();
            $data = trim($data['choice']);
            
            if (isset($pollid) && !empty($pollid)
                && isset($adminid) && !empty($adminid)
                && isset($data) && !empty($data)
            ) {
                $poll = $this->fetchTable('Polls')
                    ->findById($pollid)
                    ->contain(['Choices' => ['sort' => ['Choices.sort' => 'ASC']]])
                    ->firstOrFail();
                $dbadminid = $poll->adminid;

                $query = $this->Choices->find(
                    'all', [
                    'conditions' => ['poll_id' => $pollid, 'option' => $data]
                    ]
                );
                $number = $query->count();  // Check that choice with same name doesn't exist

                if (strcmp($dbadminid, $adminid) == 0 && $number == 0) {
                    $nextsort = $poll->choices[sizeof($poll->choices) - 1]['sort'] + 1;
                    $dbchoice = $this->Choices->newEntity(
                        [
                        'poll_id' => $poll->id,
                        'option' => trim($data),
                        'sort' => $nextsort
                        ]
                    );
                    if ($this->Choices->save($dbchoice)) {
                        $success = true;
                        // Add 'maybe' for all existing entries
                        $dbentries = $this->fetchTable('Entries')->find()
                            ->select(['name'])    
                            ->where(['poll_id' => $pollid])
                            ->group(['name']);

                        foreach ($dbentries as $entry) {
                            $dbentry = $this->fetchTable('Entries')->newEmptyEntity();
                            $dbentry = $this->fetchTable('Entries')->newEntity([
                                'poll_id' => $pollid,
                                'option' => trim($data),
                                'name' => trim($entry['name']),
                                'value' => 2
                            ]);

                            if (!$this->fetchTable('Entries')->save($dbentry)) {
                                $success = false;
                                break;
                            }
                        }
                        if ($success) {
                            $this->Flash->success(__('Option has been added.'));
                            return $this->redirect(['controller' => 'Polls', 'action' => 'edit', $pollid, $adminid]);
                        }
                    }
                }
            }
        }
        $this->Flash->error(__('Option has NOT been added!'));
        return $this->redirect($this->referer());
    }

    //------------------------------------------------------------------------

    public function delete($pollid = null, $adminid = null, $option = null)
    {
        $this->request->allowMethod(['post', 'delete']);
    
        if (isset($pollid) && !empty($pollid)
            && isset($adminid) && !empty($adminid)
            && isset($option) && !empty($option)
        ) {
            $poll = $this->fetchTable('Polls')
                ->findById($pollid)
                ->select('adminid')
                ->firstOrFail();
            $dbadminid = $poll['adminid'];

            $dbentries = $this->fetchTable('Entries')->find()
                ->where(['poll_id' => $pollid, 'option' => $option]);
            if (strcmp($dbadminid, $adminid) == 0) {
                if ($this->fetchTable('Entries')->deleteMany($dbentries)) {
                    $dbentries = $this->Choices->find()
                        ->where(['poll_id' => $pollid, 'option' => $option]);
                    if ($this->Choices->deleteMany($dbentries)) {
                        $this->Flash->success(__('Option has been deleted.'));
                        return $this->redirect(['controller' => 'Polls', 'action' => 'edit', $pollid, $adminid]);
                    }
                }
            }
        }
        $this->Flash->error(__('Option has NOT been deleted!'));
        return $this->redirect($this->referer());
    }
}
