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
 * @version   0.8.0
 */

declare(strict_types=1);

namespace App\Controller;

use Authentication\PasswordHasher\DefaultPasswordHasher;
use Cake\Core\Configure;
use Cake\Event\EventInterface;
use Cake\Mailer\Mailer;

class UsersController extends AppController
{
    private bool $DEMOMODE = false; // Updated in initialize() based on feature configuration

    public function beforeFilter(EventInterface $event): void
    {
        parent::beforeFilter($event);
        // Configure the login action to not require authentication, preventing
        // the infinite redirect loop issue
        $this->Authentication->allowUnauthenticated(
            ['login', 'logout', 'forgotPassword', 'deleteUserAndPollEntries',],
        );
    }

    public function initialize(): void
    {
        parent::initialize();

        if (Configure::read('preferendum.demoMode')) {
            $this->DEMOMODE = true;
        }

        // Add this line to check authentication result and lock your site
        $this->loadComponent('Authentication.Authentication');
    }

    //------------------------------------------------------------------------

    public function management(): ?object
    {
        $allroles = self::BACKENDROLES;
        $identity = $this->Authentication->getIdentity();
        $currentUserRole = $identity->getOriginalData()['role'];

        // Extra check needed since poll password using login credentials as well
        if (!in_array($currentUserRole, self::BACKENDROLES)) {
            $this->Authentication->logout();

            return $this->redirect(['controller' => 'Admin', 'action' => 'login']);
        } elseif (strcmp($currentUserRole, self::BACKENDROLES[0]) != 0) {
            return $this->redirect(['action' => 'edit']);
        }

        $backendusers = $this->Users->find(
            'all',
            order: ['name' => 'ASC'],
        )->select(['id', 'name', 'role', 'info'])->where(['role IN' => self::BACKENDROLES]);
        $backendusers = $backendusers->all()->toArray();

        $user = $this->Users->newEmptyEntity();

        $this->set(compact('backendusers', 'allroles', 'user'));

        return null;
    }

    //------------------------------------------------------------------------

    public function add(): object
    {
        $this->request->allowMethod(['post', 'add']);

        $identity = $this->Authentication->getIdentity();
        $currentUserRole = $identity->getOriginalData()['role'];
        if (strcmp($currentUserRole, self::BACKENDROLES[0]) != 0) {
            $this->Authentication->logout();

            return $this->redirect(['controller' => 'Admin', 'action' => 'login']);
        }

        if ($this->request->is('post', 'put')) {
            if ($this->DEMOMODE) {
                $this->Flash->error(__('DEMO MODE enabled! User creation not possible!'));

                return $this->redirect(['action' => 'management']);
            }

            $newUser = $this->Users->newEmptyEntity();
            $this->Users->patchEntity($newUser, $this->request->getData());
            if (isset($this->request->getData()['email'])) {
                $newUser['info'] = $this->request->getData()['email'];
                if (!filter_var($newUser['info'], FILTER_VALIDATE_EMAIL)) {
                    $newUser['info'] = '';
                }
            }

            // Check if user/email already exists
            $dbuser = $this->Users
                ->find()
                ->where(['role IN' => self::BACKENDROLES, 'name' => trim($newUser['name'])])
                ->first();
            if ($dbuser != null) {
                $this->Flash->error(__('User with this name already exists!'));

                return $this->redirect(['action' => 'management']);
            }
            if (isset($newUser['info']) && !empty($newUser['info'])) {
                $dbuser = $this->Users
                    ->find()
                    ->where(['role IN' => self::BACKENDROLES, 'info' => trim($newUser['info'])])
                    ->first();
                if ($dbuser != null) {
                    $this->Flash->error(__('User with this email already exists!'));

                    return $this->redirect(['action' => 'management']);
                }
            }

            $newUser['role'] = self::BACKENDROLES[$newUser['role']];
            $confirmedPassword = trim($this->request->getData()['confirmpassword']);
            if (strlen(trim($newUser['password'])) > 0 && strcmp(trim($newUser['password']), $confirmedPassword) == 0) {
                $newUser['password'] = (new DefaultPasswordHasher())->hash(trim($newUser['password']));
            } else {
                $this->Flash->error(__('Unable to add the user - password / confirmation not correct!'));

                return $this->redirect(['action' => 'management']);
            }

            if ($this->Users->save($newUser)) {
                $this->Flash->success(__('The user has been created.'));

                return $this->redirect(['action' => 'management']);
            }
            $this->Flash->error(__('Unable to add the user!'));
        }

        return $this->redirect(['action' => 'management']);
    }

    //------------------------------------------------------------------------

    public function edit(?int $editUserId = null): ?object
    {
        $allroles = self::BACKENDROLES;
        $identity = $this->Authentication->getIdentity();
        $currentUserRole = $identity->getOriginalData()['role'];
        $editUserName = '';
        $editEmail = '';
        $editUserRole = null;
        if (!in_array($currentUserRole, self::BACKENDROLES)) {
            $this->Authentication->logout();

            return $this->redirect(['controller' => 'Admin', 'action' => 'login']);
        } elseif (strcmp($currentUserRole, self::BACKENDROLES[0]) != 0) {
            // If current user is not admin, always overwrite $editUserId with current user's ID
            $editUserId = $identity->getOriginalData()['id'];
        }

        if (isset($editUserId) && !empty($editUserId)) {
            $dbEditUser = $this->Users->find()->select(
                ['name', 'role', 'info'],
            )->where(['id' => $editUserId])->firstOrFail();
            $editUserName = $dbEditUser->name;
            $editUserRole = $dbEditUser->role;
            $editEmail = $dbEditUser->info;
        }

        if (
            !isset($editUserId) || empty($editUserId) ||
            !isset($editUserRole) || empty($editUserRole) ||
            !in_array($editUserRole, self::BACKENDROLES) // Filter for pollpw "users"
        ) {
            $this->Flash->error(__('Invalid action - something went wrong!'));

            return $this->redirect(['controller' => 'Admin', 'action' => 'index']);
        }

        $backendusers = $this->Users->find(
            'all',
            order: ['name' => 'ASC'],
        )->select(['id', 'name', 'role', 'info'])->where(['role IN' => self::BACKENDROLES]);
        $backendusers = $backendusers->all()->toArray();

        $user = $this->Users->newEmptyEntity();

        $this->set(compact(
            'backendusers',
            'allroles',
            'currentUserRole',
            'editUserId',
            'editUserName',
            'editUserRole',
            'editEmail',
            'user',
        ));

        return null;
    }

    //------------------------------------------------------------------------

    public function updateUser(?int $editUserId = null): object
    {
        $this->request->allowMethod(['post', 'updateUser']);

        if ($this->request->is('post', 'put')) {
            if ($this->DEMOMODE) {
                $this->Flash->error(__('DEMO MODE enabled! Editing users is not possible!'));

                return $this->redirect(['action' => 'management']);
            }

            $updateUser = $this->request->getData();
            $identity = $this->Authentication->getIdentity();
            $currentUserRole = $identity->getOriginalData()['role'];
            if (
                !isset($editUserId) || empty($editUserId) ||
                strcmp($currentUserRole, self::BACKENDROLES[0]) != 0 // If not admin, always take current user's Id
            ) {
                $updateUser['id'] = $identity->getOriginalData()['id'];
            } else {
                $updateUser['id'] = $editUserId;
            }

            if (!in_array($currentUserRole, self::BACKENDROLES)) { // Filter for pollpw "users"
                $this->Authentication->logout();

                return $this->redirect(['controller' => 'Admin', 'action' => 'login']);
            }

            if (isset($this->request->getData()['email'])) {
                $updateUser['info'] = $this->request->getData()['email'];
                if (!filter_var($updateUser['info'], FILTER_VALIDATE_EMAIL)) {
                    $updateUser['info'] = '';
                }
            }
            if (isset($updateUser['info']) && !empty($updateUser['info'])) {
                $dbuser = $this->Users
                    ->find()
                    ->where([
                        'role IN' => self::BACKENDROLES,
                        'info' => trim($updateUser['info']),
                        'id !=' => $updateUser['id'],
                    ])
                    ->first();
                if ($dbuser != null) {
                    $this->Flash->error(__('User with this email already exists!'));

                    return $this->redirect(['action' => 'management']);
                }
            }

            $dbUser = $this->Users->findById($updateUser['id'])->firstOrFail();

            // Update name/role only if current user is admin
            if (strcmp($currentUserRole, self::BACKENDROLES[0]) == 0) {
                $updateUser['role'] = self::BACKENDROLES[$updateUser['role']];
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
                    $identity->getOriginalData()['id'] === $updateUser['id'] &&
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
            }

            $this->Users->patchEntity($dbUser, $updateUser);
            if ($this->Users->save($dbUser)) {
                $this->Flash->success(__('The user has been updated.'));

                return $this->redirect(['action' => 'management']);
            }
            $this->Flash->error(__('Unable to update the user.'));
        }

        return $this->redirect(['action' => 'management']);
    }

    //------------------------------------------------------------------------

    public function updatePassword(?int $editUserId = null): object
    {
        $this->request->allowMethod(['post', 'updatePassword']);

        if ($this->request->is('post', 'put')) {
            if ($this->DEMOMODE) {
                $this->Flash->error(__('DEMO MODE enabled! Changing the password not possible!'));

                return $this->redirect(['action' => 'edit']);
            }

            $updateUser = $this->request->getData();
            $identity = $this->Authentication->getIdentity();
            $currentUserRole = $identity->getOriginalData()['role'];
            if (
                !isset($editUserId) || empty($editUserId) ||
                strcmp($currentUserRole, self::BACKENDROLES[0]) != 0 // If not admin, always take current user's Id
            ) {
                $updateUser['id'] = $identity->getOriginalData()['id'];
            } else {
                $updateUser['id'] = $editUserId;
            }

            if (!in_array($currentUserRole, self::BACKENDROLES)) { // Filter for pollpw "users"
                $this->Authentication->logout();

                return $this->redirect(['controller' => 'Admin', 'action' => 'login']);
            }

            $confirmedPassword = trim($this->request->getData()['confirmpassword']);
            if (
                strlen(trim($updateUser['password'])) > 0 &&
                strcmp(trim($updateUser['password']), $confirmedPassword) == 0
            ) {
                $updateUser['password'] = (new DefaultPasswordHasher())->hash(trim($updateUser['password']));
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

        return $this->redirect(['action' => 'edit']);
    }

    //------------------------------------------------------------------------

    public function deleteBackendUser(?int $userid = null): object
    {
        $this->request->allowMethod(['post', 'deleteBackendUser']);

        if ($this->DEMOMODE) {
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

    public function forgotPassword(): void
    {
        if ($this->request->is('post')) {
            $user = $this->request->getData();
            $user['email'] = trim($user['email']);
            if (filter_var($user['email'], FILTER_VALIDATE_EMAIL)) {
                $dbUser = $this->Users
                    ->find()
                    ->where(['role IN' => self::BACKENDROLES, 'info' => $user['email']])
                    ->first();

                if ($dbUser != null) {
                    $newpassword = $this->generatePassword();
                    $user['password'] = (new DefaultPasswordHasher())->hash($newpassword);

                    $this->Users->patchEntity($dbUser, $user);
                    if ($this->Users->save($dbUser)) {
                        Configure::load('app_local');
                        $from = Configure::read('Email.default.from');
                        $mailer = new Mailer('default');
                        $loginurl = $this->request->scheme() . '://' . $this->request->domain() .
                            $this->request->getAttributes()['webroot'] . 'admin/login';

                        $subject = __('Password reset');
                        $mailer->viewBuilder()->setTemplate('new_backend_pw')->setLayout('default');
                        $mailer->setFrom($from)
                            ->setTo($user['email'])
                            ->setEmailFormat('text')
                            ->setSubject($subject)
                            ->setViewVars(
                                [
                                    'username' => $dbUser['name'],
                                    'loginurl' => $loginurl,
                                    'newpassword' => $newpassword,
                                ],
                            )
                            ->deliver();
                    } else {
                        $this->Flash->error(__('Something went wrong!'));
                    }
                }
            }
            $this->Flash->success(__('If a user is registered with this email address, a new password will be sent.'));
        }
    }

    //------------------------------------------------------------------------

    private function generatePassword(): string
    {
        // Source: https://alexwebdevelop.com/php-generate-random-secure-password/
        $letters = 'abcdefghijklmnopqrstuvwxyz';
        $digits = '0123456789';
        $special_chars = '!@#$%_+-=?';
        $max_similatity_perc = 20; // The maximum similarity percentage
        $minLength = 10; // The password minimum length
        $maxLength = 15; // The password maximum length

        $diffStrings = $this->Users->find('all')->select(['name'])->where(['role IN' => self::BACKENDROLES]);
        $diffStrings = array_column($diffStrings->all()->toArray(), 'name');

        // List of usable characters
        $chars = $letters . mb_strtoupper($letters) . $digits . $special_chars;
        // Set to true when a valid password is generated
        $passwordReady = false;

        while (!$passwordReady) {
            $password = '';

            // Password requirements
            $hasLowercase = false;
            $hasUppercase = false;
            $hasDigit = false;
            $hasSpecialChar = false;

            $length = random_int($minLength, $maxLength); // A random password length

            while ($length > 0) {
                $length--;

                // Choose a random character and add it to the password
                $index = random_int(0, mb_strlen($chars) - 1);
                $char = $chars[$index];
                $password .= $char;

                // Verify the requirements
                $hasLowercase = $hasLowercase || (mb_strpos($letters, $char) !== false);
                $hasUppercase = $hasUppercase || (mb_strpos(mb_strtoupper($letters), $char) !== false);
                $hasDigit = $hasDigit || (mb_strpos($digits, $char) !== false);
                $hasSpecialChar = $hasSpecialChar || (mb_strpos($special_chars, $char) !== false);
            }

            $passwordReady = ($hasLowercase && $hasUppercase && $hasDigit && $hasSpecialChar);

            // If the new password is valid, check for similarity
            if ($passwordReady) {
                foreach ($diffStrings as $string) {
                    similar_text($password, $string, $similarityPerc);
                    $passwordReady = $passwordReady && ($similarityPerc < $max_similatity_perc);
                }
            }
        }

        return $password;
    }

    //------------------------------------------------------------------------

    public function deleteUserAndPollEntries(
        ?string $pollid = null,
        ?string $adminid = null,
        ?int $userid = null,
    ): object {
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
