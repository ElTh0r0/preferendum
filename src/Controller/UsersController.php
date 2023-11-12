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

class UsersController extends AppController
{
    const DEMOMODE = false;

    public function beforeFilter(\Cake\Event\EventInterface $event)
    {
        parent::beforeFilter($event);
        // Configure the login action to not require authentication, preventing
        // the infinite redirect loop issue
        $this->Authentication->allowUnauthenticated(
            ['login', 'logout', 'deleteUserAndPollEntries',]
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
        $allroles = self::ROLES;
        $identity = $this->Authentication->getIdentity();
        $currentUserRole = $identity->getOriginalData()['role'];
        $currentUserName = $identity->getOriginalData()['name'];

        // Extra check needed since poll password using login credentials as well
        if (!in_array($currentUserRole, self::ROLES)) {
            $this->Authentication->logout();
            return $this->redirect(['controller' => 'Admin', 'action' => 'login']);
        }

        $backendusers = $this->Users->find('all', ['order' => ['name' => 'ASC']])->select(['id', 'name', 'role'])->where(['role IN' => self::ROLES]);
        $backendusers = $backendusers->all()->toArray();

        $user = $this->Users->newEmptyEntity();

        $this->set(compact('backendusers', 'allroles', 'currentUserName', 'currentUserRole', 'user'));
    }

    //------------------------------------------------------------------------

    public function addOrUpdateUser($currentUserName = null, $currentUserRole = null)
    {
        $this->request->allowMethod(['post', 'addOrUpdateUser']);

        if ($this->request->is('post', 'put')) {
            if (self::DEMOMODE) {
                $this->Flash->error(__('DEMO MODE enabled! User creation / password change is not possible!'));
                return $this->redirect(['action' => 'index']);
            }

            $newOrUpdateUser = $this->Users->newEmptyEntity();
            $this->Users->patchEntity($newOrUpdateUser, $this->request->getData());

            // Name not set, considering that current user wants to update his password
            if (!($newOrUpdateUser['name'])) {
                $newOrUpdateUser['name'] = $currentUserName;
                $newOrUpdateUser['role'] = $currentUserRole;
            } else {
                $newOrUpdateUser['role'] = self::ROLES[$newOrUpdateUser['role']];
            }

            $dbuser = $this->Users
                ->find()
                ->where(['role IN' => self::ROLES, 'name' => $newOrUpdateUser['name']])
                ->select('id')
                ->first();
            if ($dbuser != null) {  // User already exists (-> update user)
                $newOrUpdateUser['id'] = $dbuser['id'];
            }

            // Check if current user is the only admin and user tries to remove his own admin role
            if (
                strcmp($currentUserName, trim($newOrUpdateUser['name'])) == 0 &&
                strcmp($currentUserRole, self::ROLES[0]) == 0 &&
                strcmp($newOrUpdateUser['role'], self::ROLES[0]) != 0
            ) {
                $cntAdmins = $this->Users->find('all')->where(['role' => self::ROLES[0]]);
                $cntAdmins = $cntAdmins->count();
                if ($cntAdmins == 1) {
                    $this->Flash->error(__('User not updated - at least one administrator required!'));
                    return $this->redirect(['action' => 'index']);
                }
            }

            $confirmedPassword = trim($this->request->getData()['confirmpassword']);
            if (strlen(trim($newOrUpdateUser['password'])) > 0 && strcmp(trim($newOrUpdateUser['password']), $confirmedPassword) == 0) {
                $newOrUpdateUser['password'] = (new DefaultPasswordHasher)->hash(trim($newOrUpdateUser['password']));
            } else {
                $this->Flash->error(__('Unable to add/update the user - password / confirmation not correct.'));
                return $this->redirect(['action' => 'index']);
            }

            if ($this->Users->save($newOrUpdateUser)) {
                $this->Flash->success(__('The user has been created/updated.'));
                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('Unable to add the user.'));
            return $this->redirect(['action' => 'index']);
        }
    }

    //------------------------------------------------------------------------

    public function deleteBackendUser($userid = null)
    {
        $this->request->allowMethod(['post', 'deleteBackendUser']);

        $currentUserRole = $this->Authentication->getIdentity();
        $currentUserRole = $currentUserRole->getOriginalData()['role'];
        if (strcmp($currentUserRole, self::ROLES[0]) == 0) {
            if (isset($userid) && !empty($userid)) {
                $dbuser = $this->Users->findById($userid)->firstOrFail();
                if ($this->Users->delete($dbuser)) {
                    $this->Flash->success(__('User has been deleted.'));
                    return $this->redirect(['action' => 'index']);
                }
            }
        }
        $this->Flash->error(__('User {0} has NOT been deleted!', $userid));
        return $this->redirect(['action' => 'index']);
    }

    //------------------------------------------------------------------------

    public function deleteUserAndPollEntries($pollid = null, $adminid = null, $userid = null)
    {
        $this->request->allowMethod(['post', 'deleteUserAndPollEntries']);

        if (
            isset($pollid) && !empty($pollid)
            && isset($adminid) && !empty($adminid)
            && isset($userid) && !empty($userid)
        ) {
            $db = $this->fetchTable('Polls')->findById($pollid)->select('adminid')->firstOrFail();
            $dbadminid = $db['adminid'];

            if (strcmp($dbadminid, $adminid) == 0) {
                // Entries are deleted by dependency
                if ($this->Users->delete($this->Users->get($userid))) {
                    $this->Flash->success(__('Entry has been deleted.'));
                    return $this->redirect(['controller' => 'Polls', 'action' => 'edit', $pollid, $adminid]);
                }
            }
        }
        $this->Flash->error(__('Entry has NOT been deleted!'));
        return $this->redirect($this->referer());
    }
}
