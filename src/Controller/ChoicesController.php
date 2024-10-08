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
 * @version   0.7.1
 */

declare(strict_types=1);

namespace App\Controller;

class ChoicesController extends AppController
{
    public function addedit($pollid = null, $adminid = null)
    {
        if ($this->request->is('post')) {
            $choicedata = $this->request->getData();
            $choiceid = trim($choicedata['id']);
            $choicestring = trim($choicedata['choice']);
            $success = false;

            if (
                isset($pollid) && !empty($pollid)
                && isset($adminid) && !empty($adminid)
                && isset($choicestring) && !empty($choicestring)
            ) {
                $poll = $this->fetchTable('Polls')
                    ->findById($pollid)
                    ->contain(['Choices' => ['sort' => ['Choices.sort' => 'ASC']]])
                    ->firstOrFail();
                $dbadminid = $poll->adminid;

                if (strcmp($dbadminid, $adminid) == 0) {
                    $choicemax = 0;
                    if ($poll->limitentry) {
                        $choicemax = trim($choicedata['max_entries']);
                        if (!is_numeric($choicemax)) {
                            $choicemax = 0;
                        }
                    }

                    if (isset($choiceid) && !empty($choiceid)) {  // Edit existing choice
                        if ($this->isValidExisting($pollid, $choiceid)) {
                            $dbchoice = $this->Choices->findById($choiceid)->firstOrFail();
                            $this->Choices->patchEntity($dbchoice, ['option' => trim($choicestring), 'max_entries' => $choicemax]);
                            $success = $this->Choices->save($dbchoice);
                        }
                    } else if ($this->isNewChoice($pollid, $choicestring)) {  // New choice
                        $nextsort = $poll->choices[sizeof($poll->choices) - 1]['sort'] + 1;
                        $dbchoice = $this->Choices->newEntity(
                            [
                                'poll_id' => $poll->id,
                                'option' => trim($choicestring),
                                'max_entries' => $choicemax,
                                'sort' => $nextsort
                            ]
                        );

                        if ($this->Choices->save($dbchoice)) {
                            $success = $this->addNoToExisting($pollid, $dbchoice);
                        }
                    }
                    if ($success) {
                        $this->Flash->success(__('Option has been saved.'));
                        return $this->redirect(['controller' => 'Polls', 'action' => 'edit', $pollid, $adminid]);
                    }
                }
            }
        }
        $this->Flash->error(__('Option has NOT been saved!'));
        return $this->redirect($this->referer());
    }

    //------------------------------------------------------------------------

    public function swap($pollid = null, $adminid = null, $id1 = null, $id2 = null)
    {
        $this->request->allowMethod(['post', 'swap']);
        if (
            isset($pollid) && !empty($pollid) &&
            isset($adminid) && !empty($adminid) &&
            isset($id1) && isset($id2)
        ) {
            $poll = $this->fetchTable('Polls')
                ->findById($pollid)
                ->contain(['Choices' => ['sort' => ['Choices.sort' => 'ASC']]])
                ->firstOrFail();
            $dbadminid = $poll->adminid;
            $choices = $poll->choices;

            if (
                strcmp($dbadminid, $adminid) == 0 &&
                $id1 >= 0 && $id2 >= 0 &&
                $id1 < count($choices) && $id2 < count($choices) &&
                ($id1 == $id2 - 1 || $id1 == $id2 + 1)
            ) {
                [$choices[$id1]->sort, $choices[$id2]->sort] = [$choices[$id2]->sort, $choices[$id1]->sort];
                if ($this->Choices->save($choices[$id1])) {
                    if ($this->Choices->save($choices[$id2])) {
                        $this->Flash->success(__('Order has been updated.'));
                        return $this->redirect($this->referer());
                    }
                }
            }
        }

        $this->Flash->error(__('Order has NOT been updated!'));
        return $this->redirect($this->referer());
    }

    //------------------------------------------------------------------------

    public function delete($pollid = null, $adminid = null, $choiceid = null)
    {
        $this->request->allowMethod(['post', 'delete']);

        if (
            isset($pollid) && !empty($pollid)
            && isset($adminid) && !empty($adminid)
            && isset($choiceid) && !empty($choiceid)
        ) {
            $poll = $this->fetchTable('Polls')
                ->findById($pollid)
                ->select('adminid')
                ->firstOrFail();
            $dbadminid = $poll['adminid'];

            if (strcmp($dbadminid, $adminid) == 0) {
                // Entries are deleted by dependency
                if ($this->Choices->delete($this->Choices->get($choiceid))) {
                    $this->Flash->success(__('Option has been deleted.'));
                    return $this->redirect(['controller' => 'Polls', 'action' => 'edit', $pollid, $adminid]);
                }
            }
        }
        $this->Flash->error(__('Option has NOT been deleted!'));
        return $this->redirect($this->referer());
    }

    //------------------------------------------------------------------------

    private function isNewChoice($pollid, $newchoice)
    {
        $query = $this->Choices->find(
            'all',
            [
                'conditions' => ['poll_id' => $pollid, 'option' => $newchoice]
            ]
        );
        $number = $query->count();  // Check that choice with same name doesn't exist

        return ($number == 0);
    }

    //------------------------------------------------------------------------

    private function isValidExisting($pollid, $choiceid)
    {
        $query = $this->Choices->find(
            'all',
            [
                'conditions' => ['id' => $choiceid, 'poll_id' => $pollid]
            ]
        );

        return (!$query->isEmpty());
    }

    //------------------------------------------------------------------------

    private function addNoToExisting($pollid, $dbchoice)
    {
        $success = true;

        // Add 'no' for all existing entries
        $dbentries = $this->fetchTable('Entries')->find()
            ->where(['poll_id' => $pollid])
            ->contain(['Users', 'Choices'])
            ->select(['user_id' => 'Users.id'])
            ->group(['Users.id']);

        foreach ($dbentries as $user) {
            $dbentry = $this->fetchTable('Entries')->newEmptyEntity();
            $dbentry = $this->fetchTable('Entries')->newEntity([
                'choice_id' => $dbchoice->id,
                'user_id' => $user->user_id,
                'value' => 0
            ]);

            if (!$this->fetchTable('Entries')->save($dbentry)) {
                $success = false;
                break;
            }
        }

        return $success;
    }
}
