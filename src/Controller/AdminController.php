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
use Cake\Auth\DefaultPasswordHasher;

class AdminController extends AppController
{
    const POLLADMINID = 9999;
    const DEMOMODE = false;

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
        $this->loadModel('Polls');
        $this->loadComponent('Paginator');
        $polls = $this->Paginator->paginate(
            $this->Polls->find('all', ['order' => ['modified' => 'DESC']])
                ->contain(['Users'])
                ->where(['pollid IS NOT' => self::POLLADMINID])
        );

        $this->set(compact('polls'));
    }

    //------------------------------------------------------------------------

    public function usermanagement()
    {
        $polladmid = self::POLLADMINID;
        $identity = $this->Authentication->getIdentity();
        $currentUserId = $identity->getOriginalData()['id'];
        $currentUserName = $identity->getOriginalData()['name'];

        $this->loadModel('Users');
        $admins = $this->Users->find('all', ['order' => ['name' => 'ASC']])->select(['id', 'name'])->where(['pollid' => self::POLLADMINID]);
        $admins = $admins->all()->toArray();

        $user = $this->Users->newEmptyEntity();
        if ($this->request->is('post', 'put')) {
            if (self::DEMOMODE) {
                $this->Flash->error(__('DEMO MODE enabled! User creation / password change is not possible!'));
                return $this->redirect(['action' => 'usermanagement']);
            }

            $this->Users->patchEntity($user, $this->request->getData());
            $user['name'] = trim($user['name']);
            
            $dbuser = $this->Users
                ->find()
                ->where(['pollid' => self::POLLADMINID, 'name' => $user['name']])
                ->select('id')
                ->first();
            if ($dbuser != null) {  // User already exists
                $user['id'] = $dbuser['id'];
            }

            if (strlen(trim($user['info'])) > 0 && strcmp(trim($user['info']), trim($user['confirminfo'])) == 0) {
                $user['info'] = (new DefaultPasswordHasher)->hash(trim($user['info']));
            } else {
                $this->Flash->error(__('Unable to add/update the user - password / confirmation not correct.'));
                return $this->redirect(['action' => 'usermanagement']);
            }

            if ($this->Users->save($user)) {
                $this->Flash->success(__('The user has been created/updated.'));
                return $this->redirect(['action' => 'usermanagement']);
            }
            $this->Flash->error(__('Unable to add the user.'));
            return $this->redirect(['action' => 'usermanagement']);
        }
        $this->set(compact('admins', 'polladmid', 'currentUserName', 'currentUserId', 'user'));
    }

    //------------------------------------------------------------------------

    public function deleteAdmin($userid = null)
    {
        $this->request->allowMethod(['post', 'deleteAdmin']);

        $currentUserId = $this->Authentication->getIdentity();
        $currentUserId = $currentUserId->getOriginalData()['id'];
        $extUser = \Cake\Core\Configure::read('preferendum.extendedUsermanagementAccess');
        if (in_array($currentUserId, $extUser)) {
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
        $this->loadModel('Users');
        $admins = $this->Users->find()
            ->select(['name', 'info'])
            ->where(['pollid' => self::POLLADMINID]);

        $this->request->allowMethod(['get', 'post']);
        $result = $this->Authentication->getResult();
        
        // debug($result);
        // debug($result->getData()['pollid']);
        // die;
        
        // regardless of POST or GET, redirect if user is logged in
        if ($result->isValid()) {
            if ($result->getData()['pollid'] == self::POLLADMINID) {
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
