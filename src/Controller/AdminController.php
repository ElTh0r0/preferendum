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

use Cake\Auth\DefaultPasswordHasher;
use Cake\I18n\FrozenTime;

class AdminController extends AppController
{
    public function beforeFilter(\Cake\Event\EventInterface $event)
    {
        parent::beforeFilter($event);
        // Configure the login action to not require authentication, preventing
        // the infinite redirect loop issue
        $this->Authentication->allowUnauthenticated(
            ['login', 'logout',]
        );
    }

    public function initialize(): void
    {
        parent::initialize();

        // Add this line to check authentication result and lock your site
        $this->loadComponent('Authentication.Authentication');
    }

    //------------------------------------------------------------------------

    public function index()
    {
        $identity = $this->Authentication->getIdentity();
        $currentUserRole = $identity->getOriginalData()['role'];
        $adminRole = SELF::ROLES[0];
        $polladmRole = SELF::ROLES[1];

        // Extra check needed since poll password using login credentials as well
        if (!in_array($currentUserRole, self::ROLES)) {
            $this->Authentication->logout();
            return $this->redirect(['action' => 'login']);
        }

        $search = $this->request->getQuery('search');
        if ($search) {
            $query = $this->getSearchQuery($search);
        } else {
            $query = $this->fetchTable('Polls')->find('all');
        }

        $polls = $this->paginate(
            $query,
            [
                'limit' => 20,
            ]
        );

        $numpolls = $this->fetchTable('Polls')->find('all')->count();
        $numentries = $this->getNumberOfEntries();
        $numcomments = $this->getNumberOfComments();

        $this->set(compact('polls', 'numpolls', 'numentries', 'numcomments', 'currentUserRole', 'adminRole', 'polladmRole'));
    }

    //------------------------------------------------------------------------

    public function userinfo($pollid = null)
    {
        $identity = $this->Authentication->getIdentity();
        $currentUserRole = $identity->getOriginalData()['role'];

        // Extra check needed since poll password using login credentials as well
        if (!in_array($currentUserRole, self::ROLES)) {
            $this->Authentication->logout();
            return $this->redirect(['action' => 'login']);
        }

        $poll = $this->fetchTable('Polls')->find()
            ->where(['id' => $pollid])
            ->select(['title', 'userinfo'])
            ->firstOrFail();
        $polltitle = $poll->title;
        if ($poll->userinfo) {
            $userinfos = $this->getUserInfos($pollid);
        } else {
            $userinfos = array();
        }
        // debug($userinfos);
        // die;

        $this->set(compact('polltitle', 'userinfos'));
    }

    //------------------------------------------------------------------------

    public function login($pollid = null, $polladmid = null)
    {
        $this->request->allowMethod(['get', 'post']);
        $result = $this->Authentication->getResult();
        // debug($result);
        // debug($result->getData()['role']);
        // die;

        // regardless of POST or GET, redirect if user is logged in
        if ($result->isValid()) {
            if (in_array($result->getData()['role'], self::ROLES)) {
                $this->checkExpiryAndLockPolls();  // ToDo: Move to cronjob ?!

                // redirect after login success
                $target = $this->Authentication->getLoginRedirect() ?? '/admin';
                return $this->redirect($target);
            } else if (
                strcmp($result->getData()['role'], self::POLLPWROLE) == 0 &&
                isset($pollid)
            ) {
                if (isset($pollid) && isset($polladmid)) {
                    return $this->redirect(['controller' => 'Polls', 'action' => 'view', $pollid, $polladmid]);
                }
                return $this->redirect(['controller' => 'Polls', 'action' => 'view', $pollid]);
            } else {
                $this->Flash->error(__('Invalid user or password'));
                $this->Authentication->logout();
                return $this->redirect(['action' => 'login']);
            }
        }
        // display error if user submitted and authentication failed
        if ($this->request->is('post') && !$result->isValid()) {
            $this->Flash->error(__('Invalid user or password'));
            return $this->redirect(['action' => 'login']);
        }

        $this->set(compact('pollid'));
    }

    //------------------------------------------------------------------------

    public function logout()
    {
        $result = $this->Authentication->getResult();
        // regardless of POST or GET, redirect if user is logged in
        if ($result->isValid()) {
            $this->Authentication->logout();
        }
        return $this->redirect(['action' => 'login']);
    }

    //------------------------------------------------------------------------

    private function getSearchQuery($search)
    {
        $searchusers = $this->fetchTable('Entries')->find()
            ->contain(['Choices', 'Users'])
            ->where(['Users.name like'  => '%' . $search . '%'])
            ->select(['poll_id' => 'Choices.poll_id'])
            ->group(['Users.id']);
        $searchusers = $searchusers->all()->extract('poll_id');
        $foundVoters = array();
        foreach ($searchusers as $pid) {
            $foundVoters[] = $pid;
        }

        $searchcmtusers = $this->fetchTable('Comments')->find()
            ->where(['name like' => '%' . $search . '%'])
            ->select(['poll_id'])
            ->group(['poll_id']);
        $searchcmtusers = $searchcmtusers->all()->extract('poll_id');
        $foundCmtUsers = array();
        foreach ($searchcmtusers as $pid) {
            $foundCmtUsers[] = $pid;
        }

        $found = array_merge($foundVoters, $foundCmtUsers);
        if (!$found) { // Merged array is empty
            $query = $this->fetchTable('Polls')->find('all')
                ->where(['title like' => '%' . $search . '%']);
        } else {
            $query = $this->fetchTable('Polls')->find('all')
                ->where(['Or' => [
                    'title like' => '%' . $search . '%',
                    'id in' => $found
                ]]);
        }

        return $query;
    }

    //------------------------------------------------------------------------

    private function getNumberOfEntries()
    {
        $dbnumentries = $this->fetchTable('Entries')->find()
            ->contain(['Choices'])
            ->select(['Choices.poll_id'])
            ->group(['user_id']);
        $dbnumentries = $dbnumentries->all();
        $numentries = array();
        foreach ($dbnumentries as $entry) {
            $numentries[] = $entry->choice->poll_id;
        }
        $numentries = array_count_values($numentries);

        return $numentries;
    }

    //------------------------------------------------------------------------

    private function getNumberOfComments()
    {
        $numcomments = array();

        if (
            \Cake\Core\Configure::read('preferendum.alwaysAllowComments')
            || \Cake\Core\Configure::read('preferendum.opt_Comments')
        ) {
            $dbnumcomments = $this->fetchTable('Comments')->find()
                ->select(['poll_id', 'count' => 'COUNT(*)'])
                ->group(['poll_id']);
            $dbnumcomments = $dbnumcomments->all();
            $numcomments = array();
            foreach ($dbnumcomments as $comm) {
                $numcomments[$comm->poll_id] = $comm->count;
            }
        }

        return $numcomments;
    }

    //------------------------------------------------------------------------

    private function getUserInfos($pollid = null)
    {
        $dbuserinfos = $this->fetchTable('Entries')->find()
            ->contain(['Choices', 'Users'])
            ->where(['Choices.poll_id' => $pollid, 'Users.info !=' => ''])
            ->select(['name' => 'Users.name', 'info' => 'Users.info'])
            ->group(['Users.id']);

        return $dbuserinfos->toArray();
    }

    //------------------------------------------------------------------------

    private function checkExpiryAndLockPolls()
    {
        if (\Cake\Core\Configure::read('preferendum.opt_PollExpirationAfter') > 0) {
            $expiredpolls = $this->fetchTable('Polls')->query();
            $expiredpolls->update()
                ->set(['locked' => 1])
                ->where([
                    'locked' => 0,
                    'expiry >' => '0000-00-00',
                    'expiry <=' => FrozenTime::now()
                ])
                ->execute();
        }
    }
}
