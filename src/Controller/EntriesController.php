<?php
/**
 * Sprudel-ng (https://github.com/ElTh0r0/sprudel-ng)
 * Copyright (c) github.com/ElTh0r0,
 *
 * Licensed under The MIT License
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright 2020 github.com/ElTh0r0
 * @license   MIT License (https://opensource.org/licenses/mit-license.php)
 * @link      https://github.com/ElTh0r0/sprudel-ng
 * @since     0.1.0
 */
declare(strict_types=1);

namespace App\Controller;

class EntriesController extends AppController
{
    public function new($pollid)
    {
        if ($this->request->is('post')) {
            // debug($this->request->getData());
            // die();
            $data = $this->request->getData();

            $query = $this->Entries->find(
                'all', [
                'conditions' => ['pollid' => $pollid, 'name' => trim($data['name'])]
                ]
            );
            $number = $query->count();

            $this->loadModel('Polls');
            $db = $this->Polls->findByPollid($pollid)->select(['locked', 'userinfo'])->firstOrFail();
            $dblocked = $db['locked'];
            $dbuserinfo = $db['userinfo'];

            if ($number == 0 && $dblocked == 0) {
                $success = true;
                for ($i = 0; $i < sizeof($data['choices']); $i++) {
                    $dbentry = $this->Entries->newEmptyEntity();
                    $dbentry = $this->Entries->newEntity(
                        [
                        'pollid' => $pollid,
                        'option' => trim($data['choices'][$i]),
                        'name' => trim($data['name']),
                        'value' => trim($data['values'][$i])
                        ]
                    );
                    if (!$this->Entries->save($dbentry)) {
                        $success = false;
                        break;
                    }
                }
                
                if ($success) {
                    if ($dbuserinfo == 1) {
                        $userinfo = trim($data['userdetails']);
                        if (!empty($userinfo)) {
                            $this->loadModel('Users');
                            $dbentry = $this->Users->newEmptyEntity();
                            $dbentry = $this->Users->newEntity(
                                [
                                'pollid' => $pollid,
                                'name' => trim($data['name']),
                                'info' => trim($data['userdetails'])
                                ]
                            );
                            if ($this->Users->save($dbentry)) {
                                return $this->redirect(['controller' => 'Polls', 'action' => 'view', $pollid]);
                            }
                        } else {
                            return $this->redirect(['controller' => 'Polls', 'action' => 'view', $pollid]);
                        }
                    } else {
                        return $this->redirect(['controller' => 'Polls', 'action' => 'view', $pollid]);
                    }
                }
            }
        }
        $this->Flash->error(__('Unable to save your entry.'));
        return $this->redirect(['controller' => 'Polls', 'action' => 'view', $pollid]);
    }

    //------------------------------------------------------------------------

    public function delete($pollid = null, $adminid = null, $name = null)
    {
        $this->request->allowMethod(['post', 'delete']);
    
        if (isset($pollid) && !empty($pollid)
            && isset($adminid) && !empty($adminid)
            && isset($name) && !empty($name)
        ) {
            $this->loadModel('Polls');
            $db = $this->Polls->findByPollid($pollid)->select('adminid')->firstOrFail();
            $dbadminid = $db['adminid'];
            
            $dbentries = $this->Entries->find()
                ->where(['pollid' => $pollid, 'name' => $name]);
            if (strcmp($dbadminid, $adminid) == 0) {
                if ($this->Entries->deleteMany($dbentries)) {
                    $this->loadModel('Users');
                    $dbentries = $this->Users->find()
                        ->where(['pollid' => $pollid, 'name' => $name]);
                    if ($this->Users->deleteMany($dbentries)) {
                        $this->Flash->success(__('Entry has been deleted.'));
                        return $this->redirect(['controller' => 'Polls', 'action' => 'edit', $pollid, $adminid]);
                    }
                }
            }
        }
        $this->Flash->error(__('Entry has NOT been deleted!'));
        return $this->redirect($this->referer());
    }
}
