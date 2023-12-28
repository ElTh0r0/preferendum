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

            $db = $this->fetchTable('Polls')->findById($pollid)->select(['title', 'locked', 'email', 'emailentry', 'userinfo', 'anonymous', 'editentry'])->firstOrFail();
            $dbtitle = $db['title'];
            $dblocked = $db['locked'];
            $dbuserinfo = $db['userinfo'];
            $dbanonymous = $db['anonymous'];
            $dbemail = $db['email'];
            $dbemailentry = $db['emailentry'];
            $dbeditentry = $db['editentry'];

            if ($dbanonymous) {
                // Temporary name; will be renamed to _UserID once user was saved
                $newentry['name'] = 'Tmp_' . $pollid . '_' . hash("crc32", 'TEMP' . time() . '_' . random_bytes(10));
            }

            if (!$this->isNameAlreadyUsed($pollid, trim($newentry['name'])) && !($dblocked)) {
                $userinfo = '';
                if ($dbuserinfo == 1) {
                    $userinfo = trim($newentry['userdetails']);
                }
                // Create new user and save
                $new_user = $this->fetchTable('Users')->newEmptyEntity();
                $new_user = $this->fetchTable('Users')->newEntity([
                    'name' => trim($newentry['name']),
                    'password' => hash("crc32", trim($newentry['name']) . time() . random_bytes(5) . trim($newentry['name'])),
                    'info' => $userinfo
                ]);

                $success = false;
                if ($this->fetchTable('Users')->save($new_user)) {
                    $success = true;

                    if ($dbanonymous) {
                        // Rename to Anon_PollID_UserID
                        $this->fetchTable('Users')->patchEntity($new_user, ['name' => 'Anon_' . $pollid . '_' . $new_user->id]);
                        $success = $this->fetchTable('Users')->save($new_user);
                    }

                    if ($success) {
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
                                $success = false;
                                break;
                            }
                        }
                    }
                }

                if ($success) {
                    if ($dbemailentry && !empty($dbemail)) {
                        $this->sendEntryEmail($pollid, $dbemail, $dbtitle, $new_user->id, $new_user->name);
                    }

                    if ($dbeditentry) {
                        $link = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://$_SERVER[HTTP_HOST]" . $this->request->getAttributes()['webroot'] . 'polls/' . $pollid . '/NA/';
                        //debug($link . $new_user->password);
                        $this->Flash->default(
                            __('Your entry has been saved, but please note: If you want to edit your entry, you must keep this personalised link.') . '<br>' .
                                '<input type="text" id="user-edit-url" title="' . __('Copy the link and store it on your device!') . '" value="' . $link . $new_user->password . '" size="33" readonly />
                        <button type="button" class="copy-trigger entry-copy-link" data-clipboard-target="#user-edit-url" title="' . __('Copy link to clipboard!') . '"></button>',
                            [
                                'params' => [
                                    'class' => 'success',
                                    'permanent' => true,
                                    'escape' => false
                                ]
                            ]
                        );
                    } else {
                        $this->Flash->success(__('Your entry has been saved!'));
                    }

                    return $this->redirect(['controller' => 'Polls', 'action' => 'view', $pollid]);
                } else {
                    // Rollback: Delete user and already created entries (by dependency)
                    $this->fetchTable('Users')->delete($new_user);
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

            $db = $this->fetchTable('Polls')->findById($pollid)->select(['title', 'adminid', 'locked', 'email', 'emailentry', 'userinfo', 'anonymous', 'editentry'])->firstOrFail();
            $dbtitle = $db['title'];
            $dbadmid = $db['adminid'];
            $dblocked = $db['locked'];
            $dbuserinfo = $db['userinfo'];
            $dbanonymous = $db['anonymous'];
            $dbemail = $db['email'];
            $dbemailentry = $db['emailentry'];
            $dbeditallowed = $db['editentry'];

            $dbuser = $this->fetchTable('Users')->findById($userid)->firstOrFail();
            if ($dbanonymous) {
                $editentry['name'] = $dbuser['name'];
            }

            if (
                ((!($dblocked) && $dbeditallowed && strcmp($dbuser['password'], $userpw) == 0) ||  // User changes own entry
                    (isset($adminid) && strcmp($dbadmid, $adminid) == 0)) &&  // Admin changes user entry
                (!$this->isNameAlreadyUsed($pollid, trim($editentry['name'])) ||  // New name not already used
                    strcmp($dbuser['name'], trim($editentry['name'])) == 0)  // Or old and new name are the same
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

    private function isNameAlreadyUsed($pollid, $username)
    {
        $query = $this->Entries->find(
            'all',
            [
                'contain' => ['Users', 'Choices'],
                'conditions' => ['poll_id' => $pollid, 'Users.name' => $username]
            ]
        );
        $number = $query->count();

        return !($number == 0);
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
