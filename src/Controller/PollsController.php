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
use Cake\Auth\DefaultPasswordHasher;

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
                $newpoll->editentry = 0;
            }

            $pollpw = '';
            if ($newpoll->pwprotect && isset($this->request->getData()['password'])) {
                $pollpw = trim($this->request->getData()['password']);
            }
            if (strcmp($pollpw, '') == 0) {
                $newpoll->pwprotect = 0;
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

                if ($success && $newpoll->pwprotect) {
                    $success = $this->createPollPwUser($newpoll->id, $pollpw);
                }

                if ($success) {
                    if ($newpoll->pwprotect) {
                        $this->Flash->default(
                            __('Your poll has been saved.') . '<br><br>' .
                                __('Please login with your chosen password!'),
                            [
                                'params' => [
                                    'class' => 'success',
                                    'permanent' => true,
                                    'escape' => false
                                ]
                            ]
                        );
                    } else {
                        $this->Flash->success(__('Your poll has been saved.'));
                    }

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

    public function view($pollid = null, $adminid = 'NA', $userpw = null)
    {
        $poll = $this->getPollAndComments($pollid);
        if ($this->isPollAccessRestriced($poll->id, $poll->pwprotect)) {
            return $this->redirect(['controller' => 'Admin', 'action' => 'login', $poll->id, $adminid]);
        }
        $pollchoices = $this->getPollChoices($pollid);
        $dbentries = $this->getDbEntries($pollid);

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
            $this->Flash->default(__('This poll is locked - it is not possible to insert new entries or comments!'), [
                'params' => [
                    'class' => 'error',
                    'permanent' => true
                ]
            ]);
        }
        if ($poll->hideresult != 0) {
            $this->Flash->default(__('Only poll admin can see results and comments!'), [
                'params' => [
                    'permanent' => true
                ]
            ]);
        }

        // Check if valid user password was provided for editing an entry and if editing is allowed at all
        if (isset($userpw) && ($poll->editentry != 0)) {
            if (!in_array($userpw, $usermap_pw)) {
                $userpw = null;
                $usermap = array();
                $usermap_pw = array();
                $usermap_info = array();
            }
        } else {
            $userpw = null;
            $usermap = array();
            $usermap_pw = array();
            $usermap_info = array();
        }

        $newentry = $this->fetchTable('Entries')->newEmptyEntity();  // New empty entity for new entry
        $newcomment = $this->fetchTable('Comments')->newEmptyEntity();  // New empty entity for new comment

        $this->set(compact('poll', 'adminid', 'pollchoices', 'pollentries', 'usermap', 'usermap_pw', 'userpw', 'usermap_info', 'newentry', 'newcomment'));
    }

    //------------------------------------------------------------------------

    public function edit($pollid = null, $adminid = 'NA', $userpw = '')
    {
        $poll = $this->getPollAndComments($pollid);
        if (!strcmp($poll->adminid, $adminid) == 0) {
            return $this->redirect(['action' => 'view', $pollid]);
        }
        if ($this->isPollAccessRestriced($poll->id, $poll->pwprotect)) {
            return $this->redirect(['controller' => 'Admin', 'action' => 'login', $poll->id, $adminid]);
        }

        $pollchoices = $this->getPollChoices($pollid);
        $dbentries = $this->getDbEntries($pollid);

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
            $this->Flash->default(__('This poll is locked!'), [
                'params' => [
                    'class' => 'error',
                    'permanent' => true
                ]
            ]);
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
            $wasPwProtected = $poll->pwprotect;
            $dbadminid = $poll->adminid;
            if (strcmp($dbadminid, $adminid) == 0) {
                $this->Polls->patchEntity($poll, $this->request->getData());

                $pollpw = '';
                if ($poll->pwprotect && isset($this->request->getData()['password'])) {
                    $pollpw = trim($this->request->getData()['password']);
                }

                $this->validateSettings($poll);  // Call by reference

                if ($this->Polls->save($poll)) {
                    $success = true;
                    // Update password user
                    if ($wasPwProtected && $poll->pwprotect && (strcmp($pollpw, '') != 0)) {
                        $dbpwuser = $this->fetchTable('Users')->find()
                            ->select('id')
                            ->where(['name' => $poll->id, 'role' => self::POLLPWROLE])
                            ->firstOrFail();
                        $dbpwuser->password = (new DefaultPasswordHasher)->hash(trim($pollpw));
                        $success = $this->fetchTable('Users')->save($dbpwuser);
                    }

                    // Delete password user, if password protection was removed
                    if ($wasPwProtected && !$poll->pwprotect) {
                        $success = $this->deletePollPwUser($poll->id);
                    }

                    // Create password user, if password protection was added
                    if (!$wasPwProtected && $poll->pwprotect) {
                        $success = $this->createPollPwUser($poll->id, $pollpw);
                    }

                    if ($success) {
                        $this->Flash->success(__('Your poll has been updated.'));
                        return $this->redirect(['action' => 'edit', $poll->id, $adminid]);
                    }
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
                if ($this->deleteSinglePoll($poll)) {
                    $this->Flash->success(__('Poll {0} has been deleted.', $poll->title));
                    if (str_starts_with($this->referer(), '/admin')) {
                        // Stay on admin page, if deletion was triggered from there
                        return $this->redirect($this->referer());
                    }
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
            echo "ERROR: The cleanup routine can only be run from the command line (i.e. via cronjojb)." . PHP_EOL;;
            exit();
        }

        $deleteAfter = \Cake\Core\Configure::read('preferendum.deleteInactivePollsAfter');
        //minimum last changed date
        $minDate = date("Y-m-d H:i:s", strtotime("-" . $deleteAfter . " days", time()));

        echo PHP_EOL;
        echo "PREFERendum cleanup routine" . PHP_EOL;
        echo "***************************" . PHP_EOL;
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
                $this->deleteSinglePoll($poll);
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

    private function deleteSinglePoll($poll, $cli = false)
    {
        $entries = $this->fetchTable('Entries')->find()
            ->where(['poll_id' => $poll->id])
            ->contain(['Choices'])
            ->select(['id']);
        // Collect users before deleting the entries, otherwise users cannot be found anymore
        $dbusers = $this->fetchTable('Entries')->find()
            ->where(['poll_id' => $poll->id])
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
                if ($cli) {
                    echo "ERROR while deleting: " . $poll['id'] . " (error while Entries deletion)" . PHP_EOL;
                }
                return false;
            }
        }

        // Users must be deleted manually, since there is no direct dependency between Polls and Users table
        if (sizeof($users) > 0) {
            if (!$this->fetchTable('Users')->deleteAll(['id IN' => $users])) {
                if ($cli) {
                    echo "ERROR while deleting: " . $poll['id'] . " (error while Users deletion)" . PHP_EOL;
                }
                return false;
            }
        }

        // Delete poll password user
        if ($poll->pwprotect) {
            if (!$this->deletePollPwUser($poll->id)) {
                if ($cli) {
                    echo "ERROR while deleting: " . $poll['id'] . " (error while PollPwUser deletion)" . PHP_EOL;
                }
                return false;
            }
        }

        // Comments and Choices deleted automatically due to table dependency
        if (!$this->Polls->delete($poll)) {
            if ($cli) {
                echo "ERROR while deleting: " . $poll['id'] . " (error while Poll/Comments/Choices deletion)" . PHP_EOL;
            }
            return false;
        }

        return true;
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
                    $this->Authentication->logout();
                }
            } else {
                $isRestricted = true;
            }
        }

        return $isRestricted;
    }

    //------------------------------------------------------------------------

    private function isPollAccessRestriced($pollid, $pwprotect)
    {
        $isRestricted = false;

        if ($pwprotect) {
            $this->loadComponent('Authentication.Authentication');
            $result = $this->Authentication->getResult();
            if ($result->isValid()) {
                $pollpwRole = SELF::POLLPWROLE;
                $identity = $this->Authentication->getIdentity();
                $currentUserName = $identity->getOriginalData()['name'];
                $currentUserRole = $identity->getOriginalData()['role'];

                if (
                    !in_array($currentUserRole, self::ROLES) &&
                    (strcmp($currentUserName, $pollid) != 0 ||
                        strcmp($currentUserRole, $pollpwRole) != 0)
                ) {
                    $isRestricted = true;
                    $this->Authentication->logout();
                }
            } else {
                $isRestricted = true;
            }
        }

        return $isRestricted;
    }

    //------------------------------------------------------------------------

    private function createPollPwUser($pollid, $pollpw)
    {
        $dbpwuser = $this->fetchTable('Users')->newEmptyEntity();
        $dbpwuser = $this->fetchTable('Users')->newEntity(
            [
                'name' => $pollid,
                'role' => SELF::POLLPWROLE,
                'password' => (new DefaultPasswordHasher)->hash(trim($pollpw))
            ]
        );

        if ($this->fetchTable('Users')->save($dbpwuser)) {
            return true;
        }
        return false;
    }

    //------------------------------------------------------------------------

    private function deletePollPwUser($pollid)
    {
        $dbpwuser = $this->fetchTable('Users')->find()
            ->select('id')
            ->where(['name' => $pollid, 'role' => self::POLLPWROLE])
            ->firstOrFail();

        if ($this->fetchTable('Users')->delete($dbpwuser)) {
            return true;
        }
        return false;
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

    private function getDbEntries($pollid)
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
        if (!(\Cake\Core\Configure::read('preferendum.opt_PollPassword'))) {
            $poll->pwprotect = 0;
        }
    }
}
