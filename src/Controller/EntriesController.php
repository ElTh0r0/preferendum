<?php
/**
 * PREFERendum (https://github.com/ElTh0r0/preferendum)
 * Copyright (c) github.com/ElTh0r0,
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
use Cake\Mailer\Mailer;

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
                'conditions' => ['poll_id' => $pollid, 'name' => trim($data['name'])]
                ]
            );
            $number = $query->count();

            $this->loadModel('Polls');
            $db = $this->Polls->findById($pollid)->select(['title', 'locked', 'email', 'emailentry', 'userinfo'])->firstOrFail();
            $dbtitle = $db['title'];
            $dblocked = $db['locked'];
            $dbuserinfo = $db['userinfo'];
            $dbemail = $db['email'];
            $dbemailentry = $db['emailentry'];
            $link = $this->request->scheme() . '://' . $this->request->domain() . $this->request->getAttributes()['webroot'] . 'polls/' . $pollid;
            \Cake\Core\Configure::load('app_local');
            $from = \Cake\Core\Configure::read('Email.default.from');
            $entryarray = array();

            if ($number == 0 && $dblocked == 0) {
                $success = true;
                for ($i = 0; $i < sizeof($data['choices']); $i++) {
                    $dbentry = $this->Entries->newEmptyEntity();
                    $dbentry = $this->Entries->newEntity(
                        [
                        'poll_id' => $pollid,
                        'option' => trim($data['choices'][$i]),
                        'name' => trim($data['name']),
                        'value' => trim($data['values'][$i])
                        ]
                    );
                    if (!$this->Entries->save($dbentry)) {
                        $success = false;
                        break;
                    }
                    $entryarray[trim($data['choices'][$i])] = trim($data['values'][$i]);
                }
                
                if ($success) {
                    if ($dbemailentry && !empty($dbemail)) {
                        $mailer = new Mailer('default');
                        $mailer->viewBuilder()->setTemplate('new_entry')->setLayout('default');
                        $mailer->setFrom($from)
                            ->setTo($dbemail)
                            ->setEmailFormat('text')
                            ->setSubject(__('New entry in poll "{0}"', h($dbtitle)))
                            ->setViewVars(
                                [
                                'title' => $dbtitle,
                                'link' => $link,
                                'name' => trim($data['name']),
                                'entries' => $entryarray,
                                ]
                            )
                            ->deliver();
                    }

                    if ($dbuserinfo == 1) {
                        $userinfo = trim($data['userdetails']);
                        if (!empty($userinfo)) {
                            $this->loadModel('Users');
                            $dbentry = $this->Users->newEmptyEntity();
                            $dbentry = $this->Users->newEntity(
                                [
                                'poll_id' => $pollid,
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
            $db = $this->Polls->findById($pollid)->select('adminid')->firstOrFail();
            $dbadminid = $db['adminid'];
            
            $dbentries = $this->Entries->find()
                ->where(['poll_id' => $pollid, 'name' => $name]);
            if (strcmp($dbadminid, $adminid) == 0) {
                if ($this->Entries->deleteMany($dbentries)) {
                    $this->loadModel('Users');
                    $dbentries = $this->Users->find()
                        ->where(['poll_id' => $pollid, 'name' => $name]);
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
