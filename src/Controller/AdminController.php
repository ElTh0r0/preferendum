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

        $polls = $this->paginate(
            $this->fetchTable('Polls')->find('all')
                ->contain(['Users']), [
                    'limit' => 20,
                ]
        );

        $this->set(compact('polls', 'currentUserRole', 'viewerRole'));
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
