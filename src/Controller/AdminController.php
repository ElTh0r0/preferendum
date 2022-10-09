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
    const DEMOMODE = false;

    const ROLES = [
        "admin",
        "polladmin",
        "viewer",
    ];

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

        $this->loadModel('Polls');
        $polls = $this->paginate(
            $this->Polls->find('all')
                ->contain(['Users']), [
                    'limit' => 20,
                ]
        );

        $this->set(compact('polls', 'currentUserRole', 'viewerRole'));
    }

    //------------------------------------------------------------------------

    public function usermanagement()
    {
        $allroles = self::ROLES;
        $identity = $this->Authentication->getIdentity();
        $currentUserRole = $identity->getOriginalData()['role'];
        $currentUserName = $identity->getOriginalData()['name'];

        $this->loadModel('Users');
        $backendusers = $this->Users->find('all', ['order' => ['name' => 'ASC']])->select(['id', 'name', 'role'])->where(['role IN' => self::ROLES]);
        $backendusers = $backendusers->all()->toArray();

        $newOrUpdateUser = $this->Users->newEmptyEntity();
        if ($this->request->is('post', 'put')) {
            if (self::DEMOMODE) {
                $this->Flash->error(__('DEMO MODE enabled! User creation / password change is not possible!'));
                return $this->redirect(['action' => 'usermanagement']);
            }

            $this->Users->patchEntity($newOrUpdateUser, $this->request->getData());
            $newOrUpdateUser['name'] = trim($newOrUpdateUser['name']);
            $newOrUpdateUser['role'] = self::ROLES[$newOrUpdateUser['role']];

            $dbuser = $this->Users
                ->find()
                ->where(['role IN' => self::ROLES, 'name' => $newOrUpdateUser['name']])
                ->select('id')
                ->first();
            if ($dbuser != null) {  // User already exists (-> update user)
                $newOrUpdateUser['id'] = $dbuser['id'];
            }

            // Check if current user is the only admin and user tries to remove his own admin role
            if (strcmp($currentUserName, trim($newOrUpdateUser['name'])) == 0 &&
            strcmp($currentUserRole, self::ROLES[0]) == 0 &&
            strcmp($newOrUpdateUser['role'], self::ROLES[0]) != 0) {
                $cntAdmins = $this->Users->find('all')->where(['role' => self::ROLES[0]]);
                $cntAdmins = $cntAdmins->count();
                if ($cntAdmins == 1) {
                    $this->Flash->error(__('User not updated - at least one administrator required!'));
                    return $this->redirect(['action' => 'usermanagement']);
                }
            }

            if (strlen(trim($newOrUpdateUser['password'])) > 0 && strcmp(trim($newOrUpdateUser['password']), trim($newOrUpdateUser['confirmpassword'])) == 0) {
                $newOrUpdateUser['password'] = (new DefaultPasswordHasher)->hash(trim($newOrUpdateUser['password']));
            } else {
                $this->Flash->error(__('Unable to add/update the user - password / confirmation not correct.'));
                return $this->redirect(['action' => 'usermanagement']);
            }

            if ($this->Users->save($newOrUpdateUser)) {
                $this->Flash->success(__('The user has been created/updated.'));
                return $this->redirect(['action' => 'usermanagement']);
            }
            $this->Flash->error(__('Unable to add the user.'));
            return $this->redirect(['action' => 'usermanagement']);
        }
        $this->set(compact('backendusers', 'allroles', 'currentUserName', 'currentUserRole', 'newOrUpdateUser'));
    }

    //------------------------------------------------------------------------

    public function deleteBackendUser($userid = null)
    {
        $this->request->allowMethod(['post', 'deleteBackendUser']);

        $currentUserRole = $this->Authentication->getIdentity();
        $currentUserRole = $currentUserRole->getOriginalData()['role'];
        if (strcmp($currentUserRole, self::ROLES[0]) == 0) {
            if (isset($userid) && !empty($userid)) {
                $this->loadModel('Users');
                $dbuser = $this->Users->findById($userid)->firstOrFail();
                if ($this->Users->delete($dbuser)) {
                    $this->Flash->success(__('User has been deleted.'));
                    return $this->redirect(['action' => 'usermanagement']);
                }
            }
        }
        $this->Flash->error(__('User {0} has NOT been deleted!', $userid));
        return $this->redirect(['action' => 'usermanagement']);
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
