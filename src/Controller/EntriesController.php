<?php

/**
 * PREFERendum (https://github.com/ElTh0r0/preferendum)
 * Copyright (c) github.com/ElTh0r0,
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

use Cake\Mailer\Mailer;

class EntriesController extends AppController
{
    public function new($pollid)
    {
        if ($this->request->is('post')) {
            $newentry = $this->request->getData();
            // debug($newentry);
            // die();

            if (!$this->isValidEntry($pollid, $newentry)) {
                $this->Flash->error(__('Unable to save your entry.'));
                return $this->redirect(['controller' => 'Polls', 'action' => 'view', $pollid]);
            }

            $db = $this->fetchTable('Polls')->findById($pollid)->select(['title', 'locked', 'email', 'emailentry', 'userinfo'])->firstOrFail();
            $dbtitle = $db['title'];
            $dblocked = $db['locked'];
            $dbuserinfo = $db['userinfo'];
            $dbemail = $db['email'];
            $dbemailentry = $db['emailentry'];

            if ($this->isNewEntry($pollid, trim($newentry['name'])) && !($dblocked)) {
                $userinfo = '';
                if ($dbuserinfo == 1) {
                    $userinfo = trim($newentry['userdetails']);
                }
                // Create new user and save
                $new_user = $this->fetchTable('Users')->newEmptyEntity();
                $new_user = $this->fetchTable('Users')->newEntity([
                    'name' => trim($newentry['name']),
                    'password' => hash("crc32", trim($newentry['name']) . time() . trim($newentry['name'])),
                    'info' => $userinfo
                ]);

                $success = false;
                if ($this->fetchTable('Users')->save($new_user)) {
                    $success = true;

                    // Save each entry
                    for ($i = 0; $i < sizeof($newentry['choices']); $i++) {
                        $dbentry = $this->Entries->newEmptyEntity();
                        $dbentry = $this->Entries->newEntity(
                            [
                                'choice_id' => $newentry['choices'][$i],
                                'user_id' => $new_user->id,
                                'value' => trim($newentry['values'][$i])
                            ]
                        );

                        if (!$this->Entries->save($dbentry)) {
                            // Rollback: Delete user and already created entries (by dependency)
                            $this->fetchTable('Users')->delete($new_user);

                            $success = false;
                            break;
                        }
                    }
                }

                if ($success) {
                    if ($dbemailentry && !empty($dbemail)) {
                        $this->sendEntryEmail($pollid, $dbemail, $dbtitle, $new_user->id, $new_user->name);
                    }

                    return $this->redirect(['controller' => 'Polls', 'action' => 'view', $pollid]);
                }
            }
        }
        $this->Flash->error(__('Unable to save your entry.'));
        return $this->redirect(['controller' => 'Polls', 'action' => 'view', $pollid]);
    }

    //------------------------------------------------------------------------

    public function edit($pollid, $userid, $userpw, $adminid = null)
    {
        if ($this->request->is('post')) {
            $editentry = $this->request->getData();
            // debug($editentry);
            // die();

            if (!$this->isValidEntry($pollid, $editentry, $userid)) {
                $this->Flash->error(__('Unable to save your entry.'));
                return $this->redirect(['controller' => 'Polls', 'action' => 'view', $pollid]);
            }

            $db = $this->fetchTable('Polls')->findById($pollid)->select(['title', 'locked', 'email', 'emailentry', 'userinfo'])->firstOrFail();
            $dbtitle = $db['title'];
            $dblocked = $db['locked'];
            $dbuserinfo = $db['userinfo'];
            $dbemail = $db['email'];
            $dbemailentry = $db['emailentry'];

            $dbuser = $this->fetchTable('Users')->findById($userid)->firstOrFail();
            if (
                !($dblocked) &&
                strcmp($dbuser['password'], $userpw) == 0
            ) {
                // Change user
                $userinfo = '';
                if ($dbuserinfo == 1) {
                    $userinfo = trim($editentry['userdetails']);
                }
                $edituser = [
                    'name' => trim($editentry['name']),
                    'info' => $userinfo,
                ];
                $this->fetchTable('Users')->patchEntity($dbuser, $edituser);

                $success = false;
                if ($this->fetchTable('Users')->save($dbuser)) {
                    $success = true;

                    // Save each entry
                    for ($i = 0; $i < sizeof($editentry['choices']); $i++) {
                        $dbentry = $this->fetchTable('Entries')->findByChoiceId($editentry['choices'][$i])->where(['user_id' => $userid])->firstOrFail();
                        $changedentry = [
                            'value' => trim($editentry['values'][$i]),
                        ];
                        $this->fetchTable('Entries')->patchEntity($dbentry, $changedentry);

                        if (!$this->Entries->save($dbentry)) {
                            // Rollback: Delete user and already created entries (by dependency)
                            $this->fetchTable('Users')->delete($dbuser);

                            $success = false;
                            break;
                        }
                    }
                }

                if ($success) {
                    if ($dbemailentry && !empty($dbemail)) {
                        $this->sendEntryEmail($pollid, $dbemail, $dbtitle, $dbuser->id, $dbuser->name, true);
                    }

                    if (isset($adminid)) {
                        return $this->redirect(['controller' => 'Polls', 'action' => 'edit', $pollid, $adminid]);
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

    private function isValidEntry($pollid, $newentry, $userid = null)
    {
        $isValid = true;

        // Check, that values are in the expected range and had not been manipulated
        for ($i = 0; $i < sizeof($newentry['values']); $i++) {
            if (
                strcmp(trim($newentry['values'][$i]), '0') != 0 &&
                strcmp(trim($newentry['values'][$i]), '1') != 0 &&
                strcmp(trim($newentry['values'][$i]), '2') != 0
            ) {
                $isValid = false;
                return $isValid;
            }
        }

        // Check, that choices ID was not manipulated
        $dbchoices = $this->Entries->Choices->findByPollId($pollid)->select(['id'])->all();
        $validchoices = array();
        foreach ($dbchoices as $dbc) {
            $validchoices[] = $dbc['id'];
        }
        $dbchoices = array_diff($validchoices, $newentry['choices']);
        if (sizeof($dbchoices) > 0) {
            $isValid = false;
        }

        // Check for editing an entry if user belongs to poll
        if (isset($userid)) {
            $query = $this->Entries->find(
                'all',
                [
                    'contain' => ['Choices'],
                    'conditions' => ['poll_id' => $pollid, 'user_id' => $userid]
                ]
            );
            if ($query->count() != sizeof($validchoices)) {
                $isValid = false;
            }
        }

        return $isValid;
    }

    //------------------------------------------------------------------------

    private function isNewEntry($pollid, $username)
    {
        $query = $this->Entries->find(
            'all',
            [
                'contain' => ['Users', 'Choices'],
                'conditions' => ['poll_id' => $pollid, 'Users.name' => $username]
            ]
        );
        $number = $query->count();

        return ($number == 0);
    }

    //------------------------------------------------------------------------

    private function sendEntryEmail($pollid, $email, $title, $userid, $username, $changedentry = false)
    {
        $dbentries = $this->Entries->find()
            ->where(['poll_id' => $pollid, 'user_id' => $userid])
            ->contain(['Choices' => ['sort' => ['Choices.sort' => 'ASC']]])
            ->select(['Choices.option', 'value']);
        $dbentries = $dbentries->toArray();

        $link = $this->request->scheme() . '://' . $this->request->domain() . $this->request->getAttributes()['webroot'] . 'polls/' . $pollid;
        \Cake\Core\Configure::load('app_local');
        $from = \Cake\Core\Configure::read('Email.default.from');
        $subject = __('New entry in poll "{0}"', h($title));
        if ($changedentry) {
            $subject = __('Updated entry in poll "{0}"', h($title));
        }

        $mailer = new Mailer('default');
        $mailer->viewBuilder()->setTemplate('new_entry')->setLayout('default');
        $mailer->setFrom($from)
            ->setTo($email)
            ->setEmailFormat('text')
            ->setSubject($subject)
            ->setViewVars(
                [
                    'title' => $title,
                    'link' => $link,
                    'name' => $username,
                    'entries' => $dbentries,
                ]
            )
            ->deliver();
    }
}
