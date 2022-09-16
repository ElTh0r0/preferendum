<?php
/**
 * PREFERendum (https://github.com/ElTh0r0/preferendum)
 * Copyright (c) github.com/ElTh0r0
 *
 * Licensed under The MIT License
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright 2020-2022 github.com/ElTh0r0
 * @license   MIT License (https://opensource.org/licenses/mit-license.php)
 * @link      https://github.com/ElTh0r0/preferendum
 * @version   0.4.0
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
                $this->loadModel('Polls');
                $poll = $this->Polls->find()
                    ->where(['id' => $pollid])
                    ->contain(['Choices' => ['sort' => ['Choices.sort' => 'ASC']]])
                    ->firstOrFail();
                $dbadminid = $poll->adminid;

                $query = $this->Choices->find(
                    'all', [
                    'conditions' => ['poll_id' => $pollid, 'option' => $data]
                    ]
                );
                $number = $query->count();

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
                        $this->loadModel('Entries');
                        $dbentries = $this->Polls->Entries->find()
                            ->select(['name'])
                            ->where(['poll_id' => $pollid])
                            ->group(['name']);
                            $dbentries = $dbentries->all();

                        foreach ($dbentries as $entry) {
                            $dbentry = $this->Entries->newEmptyEntity();
                            $dbentry = $this->Entries->newEntity(
                                [
                                'poll_id' => $pollid,
                                'option' => trim($data),
                                'name' => trim($entry['name']),
                                'value' => 2
                                ]
                            );
                            if (!$this->Entries->save($dbentry)) {
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
            $this->loadModel('Polls');
            $db = $this->Polls->findById($pollid)->select('adminid')->firstOrFail();
            $dbadminid = $db['adminid'];
            
            $this->loadModel('Entries');
            $dbentries = $this->Entries->find()
                ->where(['poll_id' => $pollid, 'option' => $option]);
            if (strcmp($dbadminid, $adminid) == 0) {
                if ($this->Entries->deleteMany($dbentries)) {
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
