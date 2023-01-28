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
        $viewerRole = SELF::ROLES[2];
        $identity = $this->Authentication->getIdentity();
        $currentUserRole = $identity->getOriginalData()['role'];

        $uinfopolls = $this->fetchTable('Polls')->findByUserinfo(1)->select('id');
        $dbuserinfos = $this->fetchTable('Entries')->find()
            ->contain(['Choices', 'Users'])
            ->where(['Choices.poll_id IN' => $uinfopolls, 'Users.info !=' => ''])
            ->select(['poll_id' => 'Choices.poll_id', 'name' => 'Users.name', 'info' => 'Users.info'])
            ->group(['Users.id']);

        $userinfos = array();
        foreach ($dbuserinfos as $uinfo) {
            if (!isset($uinfo['poll_id'])) {
                $userinfos[$uinfo['poll_id']] = array();
            }
            $userinfos[$uinfo->poll_id][$uinfo->name] = $uinfo->info;
        }
        // debug($userinfos);
        // die;

        $dbnumentries = $this->fetchTable('Entries')->find()
            ->contain(['Choices'])
            ->select(['Choices.poll_id'])
            ->group(['user_id']); 
        $dbnumentries = $dbnumentries->all();
        $numentries = array();
        foreach($dbnumentries as $entry) {
            $numentries[] = $entry->choice->poll_id;
        }
        $numentries = array_count_values($numentries);

        if (\Cake\Core\Configure::read('preferendum.alwaysAllowComments')) {
            $dbnumcomments = $this->fetchTable('Comments')->find()
            ->select(['poll_id', 'count' => 'COUNT(*)'])
            ->group(['poll_id']); 
            $dbnumcomments = $dbnumcomments->all();
            $numcomments = array();
            foreach($dbnumcomments as $comm) {
                $numcomments[$comm->poll_id] = $comm->count;
            }
        } else {
            $numcomments = array();
        }
 
        $polls = $this->paginate(
            $this->fetchTable('Polls')->find('all'), [
                    'limit' => 20,
                ]
        );

        $this->set(compact('polls', 'numentries', 'numcomments', 'userinfos', 'currentUserRole', 'viewerRole'));
    }

    //------------------------------------------------------------------------

    public function login()
    {
        $this->request->allowMethod(['get', 'post']);
        $result = $this->Authentication->getResult();

        // debug($result);
        // debug($result->getData()['role']);
        // die;

        // regardless of POST or GET, redirect if user is logged in
        if ($result->isValid()) {
            if (in_array($result->getData()['role'], self::ROLES)) {
                // redirect after login success
                $target = $this->Authentication->getLoginRedirect() ?? '/admin';
                return $this->redirect($target);
            } else {
                $this->Flash->error(__('Invalid user or password'));
            }
        }
        // display error if user submitted and authentication failed
        if ($this->request->is('post') && !$result->isValid()) {
            $this->Flash->error(__('Invalid user or password'));
        }
    }

    //------------------------------------------------------------------------

    public function logout()
    {
        $result = $this->Authentication->getResult();
        // regardless of POST or GET, redirect if user is logged in
        if ($result->isValid()) {
            $this->Authentication->logout();
            return $this->redirect(['action' => 'login']);
        }
    }
}
