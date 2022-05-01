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
use App\Model\Entity\Choice;
use App\Model\Entity\Entry;
use App\Model\Entity\Comment;

class PollsController extends AppController
{
    public function initialize(): void
    {
        parent::initialize();
    }

    //------------------------------------------------------------------------
    
    public function add()
    {
        $poll = $this->Polls->newEmptyEntity();
        if ($this->request->is('post')) {
            $poll = $this->Polls->patchEntity($poll, $this->request->getData());
            $poll->locked = 0;

            if ($this->Polls->save($poll)) {
                $success = true;
                $this->loadModel('Choices');
                $choices = $this->request->getData('choices');
                for ($i = 0; $i < sizeof($choices); $i++) {
                    $dbchoice = $this->Choices->newEmptyEntity();
                    $dbchoice = $this->Choices->newEntity(
                        [
                        'pollid' => $poll->pollid,
                        'option' => trim($choices[$i]),
                        'sort' => $i+1
                        ]
                    );
                    if (!$this->Choices->save($dbchoice)) {
                        $success = false;
                        break;
                    }
                }
                if ($success) {
                    $this->Flash->success(__('Your poll has been saved.'));
                    if ($poll->adminLink == true) {
                        return $this->redirect(['action' => 'view', $poll->pollid, $poll->adminid]);
                    }
                    return $this->redirect(['action' => 'view', $poll->pollid]);
                }
            }
            $this->Flash->error(__('Unable to add your poll.'));
        }

        $this->set('poll', $poll);
    }

    //------------------------------------------------------------------------

    public function view($pollid = null, $adminid = 'NA')
    {
        $poll = $this->Polls->find()
            ->where(['pollid' => $pollid])
            ->contain(['Choices' => ['sort' => ['Choices.sort' => 'ASC']]])
            ->contain(['Comments' => ['sort' => ['Comments.created' => 'DESC']]])
            ->firstOrFail();
        
        $dbentries = $this->Polls->Entries->findAllByPollid($pollid);
        $pollentries = array();
        foreach ($dbentries as $entry) {
            if (!isset($entry['name'])) {
                $pollentries[$entry['name']] = array();
            }
            $pollentries[$entry->name][$entry->option] = $entry->value;
        }

        $this->loadModel('Entries');
        $entry = $this->Entries->newEmptyEntity();
        $this->loadModel('Comments');
        $comment = $this->Comments->newEmptyEntity();
        
        $locked = $this->Polls->findByPollid($pollid)->select('locked')->firstOrFail();
        $locked = $locked['locked'];
        if ($locked != 0) {
            $this->Flash->error(__('This poll is locked - it is not possible to insert new entries or comments!'));
        }
        $hiddenresult = $this->Polls->findByPollid($pollid)->select('hideresult')->firstOrFail();
        $hiddenresult = $hiddenresult['hideresult'];
        if ($hiddenresult != 0) {
            $this->Flash->default(__('Only poll admin can see results and comments!'));
        }

        $this->set(compact('poll', 'adminid', 'pollentries', 'entry', 'comment'));
    }

    //------------------------------------------------------------------------

    public function edit($pollid = null, $adminid = 'NA')
    {
        $poll = $this->Polls->find()
            ->where(['pollid' => $pollid])
            ->contain(['Choices' => ['sort' => ['Choices.sort' => 'ASC']]])
            ->contain(['Comments' => ['sort' => ['Comments.created' => 'DESC']]])
            ->firstOrFail();
        
        $dbentries = $this->Polls->Entries->findAllByPollid($pollid);
        $pollentries = array();
        foreach ($dbentries as $entry) {
            if (!isset($entry['name'])) {
                $pollentries[$entry['name']] = array();
            }
            $pollentries[$entry->name][$entry->option] = $entry->value;
        }

        $this->loadModel('Choices');
        $option = $this->Choices->newEmptyEntity();  // Needed for adding new options

        $dbadminid = $poll->adminid;
        if (strcmp($dbadminid, $adminid) == 0) {
            if ($this->request->is(['post', 'put'])) {
                $this->Polls->patchEntity($poll, $this->request->getData());
                if ($this->Polls->save($poll)) {
                    $this->Flash->success(__('Your poll has been updated.'));
                    return $this->redirect(['action' => 'edit', $poll->pollid, $adminid]);
                }
                $this->Flash->error(__('Unable to update your poll.'));
            }
        } else {
            $this->redirect(['action' => 'index']);
        }

        $locked = $this->Polls->findByPollid($pollid)->select('locked')->firstOrFail();
        $locked = $locked['locked'];
        if ($locked != 0) {
            $this->Flash->error(__('This poll is locked!'));
        }

        $this->set(compact('poll', 'adminid', 'pollentries', 'option'));
    }

    //------------------------------------------------------------------------

    public function lock($pollid = null, $adminid = null)
    {
        $this->request->allowMethod(['post', 'lock']);

        if (isset($pollid) && !empty($pollid)
            && isset($adminid) && !empty($adminid)
        ) {
            $poll = $this->Polls->findByPollid($pollid)->firstOrFail();
            $dbadminid = $poll->adminid;
            
            if (strcmp($dbadminid, $adminid) == 0) {
                if ($poll->locked == 0) {
                    $poll->locked = 1;
                } else {
                    $poll->locked = 0;
                }

                if ($this->Polls->save($poll)) {
                    if ($poll->locked == 0) {
                        $this->Flash->success(__('Poll has been unlocked'));
                    }
                    return $this->redirect(['action' => 'edit', $pollid, $adminid]);
                }
            }
        }
        $this->Flash->error(__('Poll lock has NOT been changed!', $pollid));
        return $this->redirect($this->referer());
    }

    //------------------------------------------------------------------------

    public function delete($pollid = null, $adminid = null)
    {
        $this->request->allowMethod(['post', 'delete']);

        if (isset($pollid) && !empty($pollid)
            && isset($adminid) && !empty($adminid)
        ) {
            $poll = $this->Polls->findByPollid($pollid)->firstOrFail();
            $dbadminid = $poll->adminid;
            if (strcmp($dbadminid, $adminid) == 0) {
                if ($this->Polls->delete($poll)) {
                    $this->Flash->success(__('Poll {0} has been deleted.', $poll->title));
                    return $this->redirect(['action' => 'index']);
                }
            }
        }
        $this->Flash->error(__('Poll {0} has NOT been deleted!', $pollid));
        return $this->redirect($this->referer());
    }

    //------------------------------------------------------------------------

    public function cleanup()
    {
        if (!(PHP_SAPI === 'cli')) {
            echo "ERROR: The cleanup routine can only be run from the command line (i.e. via cronjojb).";
            exit();
        }

        $deleteAfter = \Cake\Core\Configure::read('preferendum.deleteInactivePollsAfter');
        //minimum last changed date
        $minDate = date("Y-m-d H:i:s", strtotime("-" . $deleteAfter . " days", time()));
        
        echo PHP_EOL;
        echo "PREFERendum cleanup routine" . PHP_EOL;
        echo "***********************" . PHP_EOL;
        echo PHP_EOL;
        echo "This will delete every poll that was inactive" . PHP_EOL . "since " . $minDate . " (for at least " . $deleteAfter . " days)." . PHP_EOL;
        echo "You may change this value in 'config/preferendum_features.php'" . PHP_EOL;
        echo PHP_EOL;
        
        //get IDs of polls to delete
        $trash = $this->Polls->find('all')->where(['modified <' => $minDate]);
        $trash = $trash->all()->toArray();

        //remove old polls
        if (sizeof($trash) > 0) {
            foreach ($trash as $poll) {
                echo "Deleting poll: " . $poll['pollid'] . " ..." . PHP_EOL;
                if (!$this->Polls->delete($poll)) {
                    echo "ERROR while deleting: " . $poll['pollid'] . PHP_EOL;
                }
            }
        } else {
            echo "No polls inactive since " . $minDate . PHP_EOL;
        }

        echo PHP_EOL;
        echo "Deleted " . sizeof($trash) . " polls." . PHP_EOL;
        echo "Done." . PHP_EOL;
        
        $this->autoRender = false;
    }
}
