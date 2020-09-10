<?php
/**
 * Sprudel-ng (https://github.com/ElTh0r0/sprudel-ng)
 * Copyright (c) github.com/ElTh0r0
 *
 * Licensed under The MIT License
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright 2020 github.com/ElTh0r0
 * @license   MIT License (https://opensource.org/licenses/mit-license.php)
 * @link      https://github.com/ElTh0r0/sprudel-ng
 * @since     0.1.0
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
                    ->where(['pollid' => $pollid])
                    ->contain(['Choices' => ['sort' => ['Choices.sort' => 'ASC']]])
                    ->firstOrFail();
                $dbadminid = $poll->adminid;

                $query = $this->Choices->find(
                    'all', [
                    'conditions' => ['pollid' => $pollid, 'option' => $data]
                    ]
                );
                $number = $query->count();

                if (strcmp($dbadminid, $adminid) == 0 && $number == 0) {
                    $nextsort = $poll->choices[sizeof($poll->choices) - 1]['sort'] + 1;
                    $dbchoice = $this->Choices->newEntity(
                        [
                        'pollid' => $poll->pollid,
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
                            ->where(['pollid' => $pollid])
                            ->group(['name']);
                            $dbentries = $dbentries->all();

                        foreach ($dbentries as $entry) {
                            $dbentry = $this->Entries->newEmptyEntity();
                            $dbentry = $this->Entries->newEntity(
                                [
                                'pollid' => $pollid,
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
            $db = $this->Polls->findByPollid($pollid)->select('adminid')->firstOrFail();
            $dbadminid = $db['adminid'];
            
            $this->loadModel('Entries');
            $dbentries = $this->Entries->find()
                ->where(['pollid' => $pollid, 'option' => $option]);
            if (strcmp($dbadminid, $adminid) == 0) {
                if ($this->Entries->deleteMany($dbentries)) {
                    $dbentries = $this->Choices->find()
                        ->where(['pollid' => $pollid, 'option' => $option]);
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
