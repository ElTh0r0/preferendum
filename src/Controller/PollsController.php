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
 * @version   0.5.0
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

        // Show warning on main page if InstallDb script still exists
        $base = $this->request->getUri()->getPath();
        if (
            $base == '/' &&
            file_exists(APP . 'Controller/InstalldbController.php')
        ) {
            $this->Flash->error(__('File "src/Controller/InstalldbController.php" should be removed!'));
        }
    }

    //------------------------------------------------------------------------

    public function add()
    {
        if ($this->isPollCreationRestriced()) {
            return $this->redirect(['controller' => 'Admin', 'action' => 'login']);
        }

        $newpoll = $this->Polls->newEmptyEntity();
        if ($this->request->is('post') && null !== $this->request->getData('choices')) {
            $newpoll = $this->Polls->patchEntity($newpoll, $this->request->getData());

            // Some checks to prevent manipulating disabled input fields through browser tools
            if (\Cake\Core\Configure::read('preferendum.alwaysUseAdminLinks')) {
                $newpoll->adminid = true;
            }
            if (!$newpoll->adminid) {
                $newpoll->hideresult = 0;
            }
            $this->validateSettings($newpoll);  // Call by reference

            if ($this->Polls->save($newpoll)) {
                $success = true;
                $choices = $this->request->getData('choices');
                for ($i = 0; $i < sizeof($choices); $i++) {
                    $dbchoice = $this->fetchTable('Choices')->newEmptyEntity();
                    $dbchoice = $this->fetchTable('Choices')->newEntity(
                        [
                            'poll_id' => $newpoll->id,
                            'option' => trim($choices[$i]),
                            'sort' => $i + 1
                        ]
                    );
                    if (!$this->fetchTable('Choices')->save($dbchoice)) {
                        $success = false;
                        break;
                    }
                }
                if ($success) {
                    $this->Flash->success(__('Your poll has been saved.'));
                    if ($newpoll->adminid == true) {
                        return $this->redirect(['action' => 'view', $newpoll->id, $newpoll->adminid]);
                    }
                    return $this->redirect(['action' => 'view', $newpoll->id]);
                } else {
                    $this->Polls->delete($newpoll);
                }
            }
            $this->Flash->error(__('Unable to add your poll.'));
        }

        $this->set('poll', $newpoll);
    }

    //------------------------------------------------------------------------

    public function view($pollid = null, $adminid = 'NA')
    {
        $poll = $this->getPollAndComments($pollid);
        $pollchoices = $this->getPollChoices($pollid);
        $dbentries = $this->getDbEntriesView($pollid);

        $pollentries = array();
        foreach ($dbentries as $entry) {
            if (!isset($entry['name'])) {
                $pollentries[$entry['name']] = array();
            }
            $pollentries[$entry->name][$entry->choice_id] = $entry->value;
        }

        if ($poll->locked != 0) {
            $this->Flash->error(__('This poll is locked - it is not possible to insert new entries or comments!'));
        }
        if ($poll->hideresult != 0) {
            $this->Flash->default(__('Only poll admin can see results and comments!'));
        }

        $newentry = $this->fetchTable('Entries')->newEmptyEntity();  // New empty entity for new entry
        $newcomment = $this->fetchTable('Comments')->newEmptyEntity();  // New empty entity for new comment

        $this->set(compact('poll', 'adminid', 'pollchoices', 'pollentries', 'newentry', 'newcomment'));
    }

    //------------------------------------------------------------------------

    public function edit($pollid = null, $adminid = 'NA', $userpw = '')
    {
        $poll = $this->getPollAndComments($pollid);
        $pollchoices = $this->getPollChoices($pollid);
        $dbentries = $this->getDbEntriesEdit($pollid);

        $pollentries = array();
        $usermap = array();
        $usermap_pw = array();
        $usermap_info = array();
        // TODO: Think about better implementation for passing all the data...
        foreach ($dbentries as $entry) {
            if (!isset($pollentries[$entry['name']])) {
                $pollentries[$entry['name']] = array();
                $usermap[$entry['name']] = $entry->user_id;
                $usermap_pw[$entry['name']] = $entry->user_pw;
                $usermap_info[$entry['name']] = $entry->user_info;
            }
            $pollentries[$entry->name][$entry->choice_id] = $entry->value;
        }

        if ($poll->locked != 0) {
            $this->Flash->error(__('This poll is locked!'));
        }

        $newchoice = $this->fetchTable('Choices')->newEmptyEntity();  // Needed for adding new options
        $newentry = $this->fetchTable('Entries')->newEmptyEntity();  // New empty entity for new entry

        $this->set(compact('poll', 'adminid', 'pollchoices', 'pollentries', 'usermap', 'usermap_pw', 'userpw', 'usermap_info', 'newchoice', 'newentry'));
    }

    //------------------------------------------------------------------------

    public function update($pollid = null, $adminid = null)
    {
        $this->request->allowMethod(['post', 'update']);

        if (
            $this->request->is('post', 'put') &&
            isset($pollid) && !empty($pollid) &&
            isset($adminid) && !empty($adminid)
        ) {
            $poll = $this->Polls->findById($pollid)->firstOrFail();
            $dbadminid = $poll->adminid;
            if (strcmp($dbadminid, $adminid) == 0) {
                $this->Polls->patchEntity($poll, $this->request->getData());
                $this->validateSettings($poll);  // Call by reference

                if ($this->Polls->save($poll)) {
                    $this->Flash->success(__('Your poll has been updated.'));
                    return $this->redirect(['action' => 'edit', $poll->id, $adminid]);
                }
                $this->Flash->error(__('Unable to update your poll.'));
            } else {
                return $this->redirect(['action' => 'index']);
            }
        }
    }

    //------------------------------------------------------------------------

    public function togglelock($pollid = null, $adminid = null)
    {
        $this->request->allowMethod(['post', 'togglelock']);

        if (
            isset($pollid) && !empty($pollid)
            && isset($adminid) && !empty($adminid)
        ) {
            $poll = $this->Polls->findById($pollid)->firstOrFail();
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

        if (
            isset($pollid) && !empty($pollid)
            && isset($adminid) && !empty($adminid)
        ) {
            $poll = $this->Polls->findById($pollid)->firstOrFail();
            $dbadminid = $poll->adminid;
            if (strcmp($dbadminid, $adminid) == 0) {
                $success = true;
                $entries = $this->fetchTable('Entries')->find()
                    ->where(['poll_id' => $pollid])
                    ->contain(['Choices'])
                    ->select(['id']);
                // Collect users before deleting the entries, otherwise users cannot be found anymore
                $dbusers = $this->fetchTable('Entries')->find()
                    ->where(['poll_id' => $pollid])
                    ->contain(['Users', 'Choices'])
                    ->select(['user_id' => 'Users.id'])
                    ->group(['user_id'])->all();
                $users = array();
                foreach ($dbusers as $usr) {
                    $users[] = $usr['user_id'];
                }

                // Entries must be deleted manually, since there is no direct dependency between Polls and Entries table
                if ($entries->count() > 0) {
                    if (!$this->fetchTable('Entries')->deleteAll(['id IN' => $entries])) {
                        $success = false;
                    }
                }

                if ($success) {
                    // Users must be deleted manually, since there is no direct dependency between Polls and Users table
                    if (sizeof($users) > 0) {
                        if (!$this->fetchTable('Users')->deleteAll(['id IN' => $users])) {
                            $success = false;
                        }
                    }

                    if ($success) {
                        // Comments and Choices deleted automatically due to table dependency
                        if ($this->Polls->delete($poll)) {
                            $this->Flash->success(__('Poll {0} has been deleted.', $poll->title));
                            if ($this->referer() == '/admin') {
                                // Stay on admin page, if deletion was triggered from there
                                return $this->redirect($this->referer());
                            }
                            return $this->redirect(['action' => 'index']);
                        }
                    }
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
                echo "Deleting poll: " . $poll['id'] . " ..." . PHP_EOL;

                $userentries = $this->fetchTable('Entries')->find()
                    ->where(['poll_id' => $poll['id']])
                    ->contain(['Users'])
                    ->select(['user_id' => 'Users.id'])
                    ->group(['user_id']);

                if ($userentries->count() > 0) {
                    if (!$this->fetchTable('Users')->deleteAll(['id IN' => $userentries])) {
                        echo "ERROR while deleting: " . $poll['id'] . " (error while user deletion)" . PHP_EOL;
                    }
                }
                if (!$this->Polls->delete($poll)) {
                    echo "ERROR while deleting: " . $poll['id'] . PHP_EOL;
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

    //------------------------------------------------------------------------

    private function isPollCreationRestriced()
    {
        $isRestricted = false;

        if (
            \Cake\Core\Configure::read('preferendum.adminInterface') &&
            \Cake\Core\Configure::read('preferendum.restrictPollCreation')
        ) {
            $this->loadComponent('Authentication.Authentication');
            $result = $this->Authentication->getResult();
            if ($result->isValid()) {
                $adminRole = SELF::ROLES[0];
                $polladmRole = SELF::ROLES[1];
                $identity = $this->Authentication->getIdentity();
                $currentUserRole = $identity->getOriginalData()['role'];
                if (
                    strcmp($currentUserRole, $adminRole) != 0 &&
                    strcmp($currentUserRole, $polladmRole) != 0
                ) {
                    $isRestricted = true;
                }
            } else {
                $isRestricted = true;
            }
        }

        return $isRestricted;
    }

    //------------------------------------------------------------------------

    private function getPollAndComments($pollid)
    {
        $poll = $this->Polls->find()
            ->where(['id' => $pollid])
            ->contain(['Comments' => ['sort' => ['Comments.created' => 'DESC']]])
            ->firstOrFail();

        return $poll;
    }

    //------------------------------------------------------------------------

    private function getPollChoices($pollid)
    {
        $dbchoices = $this->fetchTable('Choices')->find()
            ->where(['poll_id' => $pollid])
            ->select(['id', 'option'])
            ->order(['sort' => 'ASC']);

        return $dbchoices->toArray();
    }

    //------------------------------------------------------------------------

    private function getDbEntriesView($pollid)
    {
        $dbentries = $this->fetchTable('Entries')->find()
            ->where(['poll_id' => $pollid])
            ->contain(['Choices' => ['sort' => ['Choices.sort' => 'ASC']]])
            ->contain(['Users'])
            ->select(['choice_id', 'value', 'name' => 'Users.name']);

        return $dbentries;
    }

    //------------------------------------------------------------------------

    private function getDbEntriesEdit($pollid)
    {
        $dbentries = $this->fetchTable('Entries')->find()
            ->where(['poll_id' => $pollid])
            ->contain(['Choices' => ['sort' => ['Choices.sort' => 'ASC']]])
            ->contain(['Users'])
            ->select(['choice_id', 'value', 'name' => 'Users.name', 'user_id' => 'Users.id', 'user_pw' => 'Users.password', 'user_info' => 'Users.info']);

        return $dbentries;
    }

    //------------------------------------------------------------------------

    // Call by reference - $poll changed directly.
    private function validateSettings(&$poll)
    {
        if (
            !\Cake\Core\Configure::read('preferendum.alwaysAllowComments')
            && !\Cake\Core\Configure::read('preferendum.opt_Comments')
        ) {
            $poll->comment = 0;
            $poll->emailcomment = 0;
        }
        if (!$poll->comment && \Cake\Core\Configure::read('preferendum.opt_Comments')) {
            $poll->emailcomment = 0;
        }
        if (!filter_var($poll->email, FILTER_VALIDATE_EMAIL)) {
            $poll->emailentry = 0;
            $poll->emailcomment = 0;
        }
        if (!($poll->emailentry) && !($poll->emailcomment)) {
            $poll->email = '';
        }
    }
}
