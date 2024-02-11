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
 * @version   0.6.0
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

    public function management()
    {
        $allroles = self::BACKENDROLES;
        $identity = $this->Authentication->getIdentity();
        $currentUserRole = $identity->getOriginalData()['role'];

        // Extra check needed since poll password using login credentials as well
        if (!in_array($currentUserRole, self::BACKENDROLES)) {
            $this->Authentication->logout();
            return $this->redirect(['controller' => 'Admin', 'action' => 'login']);
        } else if (strcmp($currentUserRole, self::BACKENDROLES[0]) != 0) {
            return $this->redirect(['action' => 'edit']);
        }

        $backendusers = $this->Users->find('all', ['order' => ['name' => 'ASC']])->select(['id', 'name', 'role'])->where(['role IN' => self::BACKENDROLES]);
        $backendusers = $backendusers->all()->toArray();

        $user = $this->Users->newEmptyEntity();

        $this->set(compact('backendusers', 'allroles', 'user'));
    }

    //------------------------------------------------------------------------

    public function add()
    {
        $this->request->allowMethod(['post', 'add']);

        $identity = $this->Authentication->getIdentity();
        $currentUserRole = $identity->getOriginalData()['role'];
        if (strcmp($currentUserRole, self::BACKENDROLES[0]) != 0) {
            $this->Authentication->logout();
            return $this->redirect(['controller' => 'Admin', 'action' => 'login']);
        }

        if ($this->request->is('post', 'put')) {
            if (self::DEMOMODE) {
                $this->Flash->error(__('DEMO MODE enabled! User creation not possible!'));
                return $this->redirect(['action' => 'management']);
            }

            $newUser = $this->Users->newEmptyEntity();
            $this->Users->patchEntity($newUser, $this->request->getData());

            // Check if user already exists
            $dbuser = $this->Users
                ->find()
                ->where(['role IN' => self::BACKENDROLES, 'name' => trim($newUser['name'])])
                ->first();
            if ($dbuser != null) {
                $this->Flash->error(__('User with this name already exists!'));
                return $this->redirect(['action' => 'management']);
            }

            $newUser['role'] = self::BACKENDROLES[$newUser['role']];
            $confirmedPassword = trim($this->request->getData()['confirmpassword']);
            if (strlen(trim($newUser['password'])) > 0 && strcmp(trim($newUser['password']), $confirmedPassword) == 0) {
                $newUser['password'] = (new DefaultPasswordHasher)->hash(trim($newUser['password']));
            } else {
                $this->Flash->error(__('Unable to add the user - password / confirmation not correct!'));
                return $this->redirect(['action' => 'management']);
            }

            if ($this->Users->save($newUser)) {
                $this->Flash->success(__('The user has been created.'));
                return $this->redirect(['action' => 'management']);
            }
            $this->Flash->error(__('Unable to add the user!'));
            return $this->redirect(['action' => 'management']);
        }
    }

    //------------------------------------------------------------------------

    public function edit($editUserId = null)
    {
        $allroles = self::BACKENDROLES;
        $identity = $this->Authentication->getIdentity();
        $currentUserRole = $identity->getOriginalData()['role'];
        $editUserName = '';
        $editUserRole = null;
        if (!in_array($currentUserRole, self::BACKENDROLES)) {
            $this->Authentication->logout();
            return $this->redirect(['controller' => 'Admin', 'action' => 'login']);
        } else if (strcmp($currentUserRole, self::BACKENDROLES[0]) != 0) {
            // If current user is not admin, always overwrite $editUserId with current user's ID
            $editUserId = $identity->getOriginalData()['id'];
        }

        if (isset($editUserId) && !empty($editUserId)) {
            $dbEditUser = $this->Users->find()->select(['name', 'role'])->where(['id' => $editUserId])->firstOrFail();
            $editUserName = $dbEditUser->name;
            $editUserRole = $dbEditUser->role;
        }

        if (
            !isset($editUserId) || empty($editUserId) ||
            !isset($editUserRole) || empty($editUserRole) ||
            !in_array($editUserRole, self::BACKENDROLES)  // Filter for pollpw "users"
        ) {
            $this->Flash->error(__('Invalid action - something went wrong!'));
            return $this->redirect(['controller' => 'Admin', 'action' => 'index']);
        }

        $backendusers = $this->Users->find('all', ['order' => ['name' => 'ASC']])->select(['id', 'name', 'role'])->where(['role IN' => self::BACKENDROLES]);
        $backendusers = $backendusers->all()->toArray();

        $user = $this->Users->newEmptyEntity();

        $this->set(compact('backendusers', 'allroles', 'currentUserRole', 'editUserId', 'editUserName', 'editUserRole', 'user'));
    }

    //------------------------------------------------------------------------

    public function updateNameRole($editUserId = null)
    {
        $this->request->allowMethod(['post', 'updateNameRole']);

        if ($this->request->is('post', 'put')) {
            if (self::DEMOMODE) {
                $this->Flash->error(__('DEMO MODE enabled! Editing users is not possible!'));
                return $this->redirect(['action' => 'management']);
            }

            $identity = $this->Authentication->getIdentity();
            $currentUserRole = $identity->getOriginalData()['role'];
            if (strcmp($currentUserRole, self::BACKENDROLES[0]) != 0) {
                $this->Authentication->logout();
                return $this->redirect(['controller' => 'Admin', 'action' => 'login']);
            }

            $updateUser = $this->request->getData();
            $updateUser['role'] = self::BACKENDROLES[$updateUser['role']];
            $dbUser = $this->Users->findById($editUserId)->firstOrFail();

            // Check if user already exists
            if (strcmp(trim($updateUser['name']), $dbUser['name']) != 0) {
                $dbuser = $this->Users
                    ->find()
                    ->where(['role IN' => self::BACKENDROLES, 'name' => trim($updateUser['name'])])
                    ->first();
                if ($dbuser != null) {
                    $this->Flash->error(__('User with this name already exists!'));
                    return $this->redirect(['action' => 'management']);
                }
            }

            // Check if current user is the only admin and user tries to remove his own admin role
            if (
                strcmp(strval($identity->getOriginalData()['id']), $editUserId) == 0 &&
                strcmp($currentUserRole, self::BACKENDROLES[0]) == 0 &&
                strcmp($updateUser['role'], self::BACKENDROLES[0]) != 0
            ) {
                $cntAdmins = $this->Users->find('all')->where(['role' => self::BACKENDROLES[0]]);
                $cntAdmins = $cntAdmins->count();
                if ($cntAdmins == 1) {
                    $this->Flash->error(__('User not updated - at least one administrator required!'));
                    return $this->redirect(['action' => 'management']);
                }
            }

            $this->Users->patchEntity($dbUser, $updateUser);
            if ($this->Users->save($dbUser)) {
                $this->Flash->success(__('The user has been updated.'));
                return $this->redirect(['action' => 'management']);
            }
            $this->Flash->error(__('Unable to update the user.'));
            return $this->redirect(['action' => 'management']);
        }
    }

    //------------------------------------------------------------------------

    public function updatePassword($editUserId = null)
    {
        $this->request->allowMethod(['post', 'updatePassword']);

        if ($this->request->is('post', 'put')) {
            if (self::DEMOMODE) {
                $this->Flash->error(__('DEMO MODE enabled! Changing the password not possible!'));
                return $this->redirect(['action' => 'edit']);
            }

            $updateUser = $this->request->getData();
            $identity = $this->Authentication->getIdentity();
            $currentUserRole = $identity->getOriginalData()['role'];
            if (
                !isset($editUserId) || empty($editUserId) ||
                strcmp($currentUserRole, self::BACKENDROLES[0]) != 0  // If not admin, always take current user's Id
            ) {
                $updateUser['id'] = $identity->getOriginalData()['id'];
            } else {
                $updateUser['id'] = $editUserId;
            }

            if (!in_array($currentUserRole, self::BACKENDROLES)) {  // Filter for pollpw "users"
                $this->Authentication->logout();
                return $this->redirect(['controller' => 'Admin', 'action' => 'login']);
            }

            $confirmedPassword = trim($this->request->getData()['confirmpassword']);
            if (strlen(trim($updateUser['password'])) > 0 && strcmp(trim($updateUser['password']), $confirmedPassword) == 0) {
                $updateUser['password'] = (new DefaultPasswordHasher)->hash(trim($updateUser['password']));
            } else {
                $this->Flash->error(__('Unable to update the password / confirmation not correct.'));
                return $this->redirect(['action' => 'edit', $updateUser['id']]);
            }

            $dbUser = $this->Users->findById($updateUser['id'])->firstOrFail();
            $this->Users->patchEntity($dbUser, $updateUser);

            if ($this->Users->save($dbUser)) {
                $this->Flash->success(__('The password has been updated.'));
                return $this->redirect(['action' => 'edit', $updateUser['id']]);
            }
            $this->Flash->error(__('Unable to save the new password!'));
            return $this->redirect(['action' => 'edit', $updateUser['id']]);
        }
    }

    //------------------------------------------------------------------------

    public function deleteBackendUser($userid = null)
    {
        $this->request->allowMethod(['post', 'deleteBackendUser']);

        if (self::DEMOMODE) {
            $this->Flash->error(__('DEMO MODE enabled! User deletion is not possible!'));
            return $this->redirect(['action' => 'management']);
        }

        $currentUserRole = $this->Authentication->getIdentity();
        $currentUserRole = $currentUserRole->getOriginalData()['role'];
        if (strcmp($currentUserRole, self::BACKENDROLES[0]) == 0) {
            if (isset($userid) && !empty($userid)) {
                $dbuser = $this->Users->findById($userid)->firstOrFail();
                if ($this->Users->delete($dbuser)) {
                    $this->Flash->success(__('User has been deleted.'));
                    return $this->redirect(['action' => 'management']);
                }
            }
        }
        $this->Flash->error(__('User {0} has NOT been deleted!', $userid));
        return $this->redirect(['action' => 'management']);
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
