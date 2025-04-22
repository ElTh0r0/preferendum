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
 * @version   0.8.0
 */

declare(strict_types=1);

namespace App\Controller;

use Cake\Collection\Collection;
use Cake\Core\Configure;
use Cake\Mailer\Mailer;

class EntriesController extends AppController
{
    public function new(string $pollid): object
    {
        if ($this->request->is('post')) {
            $newentry = $this->request->getData();
            // debug($newentry);
            // die();

            if (!$this->isValidEntry($pollid, $newentry)) {
                $this->Flash->error(__('Unable to save your entry.'));

                return $this->redirect(['controller' => 'Polls', 'action' => 'view', $pollid]);
            }

            $poll = $this->fetchTable('Polls')->findById($pollid)->select([
                'title',
                'locked',
                'email',
                'emailentry',
                'userinfo',
                'anonymous',
                'editentry',
                'limitentry',
            ])->firstOrFail();

            if ($poll->anonymous) {
                // Temporary name; will be renamed to _UserID once user was saved
                $newentry['name'] = 'Tmp_' . $pollid . '_' . hash('crc32', 'TEMP' . time() . '_' . random_bytes(10));
            }

            if (!$this->isNameAlreadyUsed($pollid, trim($newentry['name'])) && !$poll->locked) {
                $userinfo = '';
                if ($poll->userinfo) {
                    $userinfo = trim($newentry['userdetails']);
                }
                // Create new user and save
                $new_user = $this->fetchTable('Users')->newEmptyEntity();
                $new_user = $this->fetchTable('Users')->newEntity([
                    'name' => trim($newentry['name']),
                    'password' => hash(
                        'crc32',
                        trim($newentry['name']) . time() . random_bytes(5) . trim($newentry['name']),
                    ),
                    'info' => $userinfo,
                ]);

                $success = false;
                if ($this->fetchTable('Users')->save($new_user)) {
                    $success = true;

                    if ($poll->anonymous) {
                        // Rename to Anon_PollID_UserID
                        $this->fetchTable('Users')->patchEntity(
                            $new_user,
                            ['name' => 'Anon_' . $pollid . '_' . $new_user->id],
                        );
                        $success = $this->fetchTable('Users')->save($new_user);
                    }

                    $max_entries = [];
                    $already_yes = [];
                    if ($poll->limitentry) {
                        $max_entries = $this->fetchTable('Choices')
                            ->findByPollId($pollid)
                            ->select(['id', 'max_entries']);
                        $max_entries = (new Collection($max_entries))->combine('id', 'max_entries')->toArray();
                        $already_yes = $this->getNumOfYesEntries($pollid);
                    }

                    if ($success) {
                        // Save each entry
                        $newEntryNumChoices = count($newentry['choices']);
                        for ($i = 0; $i < $newEntryNumChoices; $i++) {
                            if (
                                $poll->limitentry &&
                                count($max_entries) == $newEntryNumChoices &&
                                count($already_yes) == $newEntryNumChoices
                            ) {
                                if (
                                    $already_yes[$newentry['choices'][$i]] >= $max_entries[$newentry['choices'][$i]] &&
                                    $max_entries[$newentry['choices'][$i]] != 0
                                ) {
                                    $newentry['values'][$i] = '0';
                                }
                            }
                            $dbentry = $this->Entries->newEmptyEntity();
                            $dbentry = $this->Entries->newEntity(
                                [
                                    'choice_id' => $newentry['choices'][$i],
                                    'user_id' => $new_user->id,
                                    'value' => trim($newentry['values'][$i]),
                                ],
                            );

                            if (!$this->Entries->save($dbentry)) {
                                $success = false;
                                break;
                            }
                        }
                    }
                }

                if ($success) {
                    if ($poll->emailentry && !empty($poll->email)) {
                        $this->sendEntryEmail($pollid, $poll->email, $poll->title, $new_user->id, $new_user->name);
                    }

                    if ($poll->editentry) {
                        $link = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http') .
                            '://' . $_SERVER['HTTP_HOST'] . $this->request->getAttributes()['webroot'] .
                            'polls/' . $pollid . '/NA/';
                        // debug($link . $new_user->password);

                        $sendLink = '';
                        if (Configure::read('preferendum')['opt_SendChangeEntryLink']) {
                            // Ugly hack: Must create form manually as the FormHelper is not available in Controllers
                            $sendLink = '<br><br>' .
                                __('Optionally, you can receive your personalised link by email:') . '<br>' .
                                '<form method="post" action="sendpersonallink">' .
                                '<input type="hidden" name="_csrfToken" autocomplete="off" value="' .
                                $this->request->getAttribute('csrfToken') . '">' .
                                '<input type="hidden" name="pollname" autocomplete="off" value="' .
                                $poll->title . '">' .
                                '<input type="hidden" name="name" autocomplete="off" value="' .
                                $new_user->name . '">' .
                                '<input type="hidden" name="link" autocomplete="off" value="' .
                                $link . $new_user->password . '">' .
                                '<input type="email" name="email" id="user-edit-url" required="required" 
                                placeholder="' . __('Email for receiving your personal link') .
                                '" size="31" autocomplete="email"> <button type="submit" class="entry-email-link" 
                                title="' . __('Send email') . '"></button></form>';
                        }

                        $this->Flash->default(
                            __('Your entry has been saved, but please note: If you want to edit your entry, you must 
                            keep this personalised link.') .
                                '<br><input type="text" id="user-edit-url" title="' .
                                __('Copy the link and store it on your device!') .
                                '" value="' . $link . $new_user->password . '" size="31" readonly> 
                                <button type="button" class="copy-trigger entry-copy-link-user" 
                                data-clipboard-target="#user-edit-url" title="' .
                                __('Copy link to clipboard!') . '"></button>' . $sendLink,
                            [
                                'params' => [
                                    'class' => 'success',
                                    'permanent' => true,
                                    'escape' => false,
                                ],
                            ],
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

    public function edit(string $pollid, int $userid, string $userpw, ?string $adminid = null): object
    {
        if ($this->request->is('post')) {
            $editentry = $this->request->getData();
            // debug($editentry);
            // die();

            if (!$this->isValidEntry($pollid, $editentry, $userid)) {
                $this->Flash->error(__('Unable to save your entry.'));

                return $this->redirect(['controller' => 'Polls', 'action' => 'view', $pollid]);
            }

            $poll = $this->fetchTable('Polls')->findById($pollid)->select([
                'title',
                'adminid',
                'locked',
                'email',
                'emailentry',
                'userinfo',
                'anonymous',
                'editentry',
                'limitentry',
            ])->firstOrFail();

            $dbuser = $this->fetchTable('Users')->findById($userid)->firstOrFail();
            if ($poll->anonymous) {
                $editentry['name'] = $dbuser['name'];
            }

            if (
                ((!$poll->locked && $poll->editentry && strcmp($dbuser['password'], $userpw) == 0) || // User changes own entry
                    (isset($adminid) && strcmp($poll->adminid, $adminid) == 0)) && // Admin changes user entry
                (!$this->isNameAlreadyUsed($pollid, trim($editentry['name'])) || // New name not already used
                    strcmp($dbuser['name'], trim($editentry['name'])) == 0) // Or old and new name are the same
            ) {
                // Change user
                $userinfo = '';
                if ($poll->userinfo) {
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

                    $max_entries = [];
                    $already_yes = [];
                    if ($poll->limitentry) {
                        $max_entries = $this->fetchTable('Choices')->findByPollId($pollid)
                            ->select(['id', 'max_entries']);
                        $max_entries = (new Collection($max_entries))->combine('id', 'max_entries')->toArray();
                        $already_yes = $this->getNumOfYesEntries($pollid);
                    }

                    // Save each entry
                    $editEntryNumChoices = count($editentry['choices']);
                    for ($i = 0; $i < $editEntryNumChoices; $i++) {
                        $dbentry = $this->fetchTable('Entries')->findByChoiceId($editentry['choices'][$i])
                            ->where(['user_id' => $userid])->firstOrFail();

                        if (
                            $poll->limitentry &&
                            count($max_entries) == $editEntryNumChoices &&
                            count($already_yes) == $editEntryNumChoices
                        ) {
                            // Decrease already_yes if old entry was already yes/maybe before max_entries check
                            // Otherwise changing an existing entry would reset yes/maybe selection if max_entries is reached
                            if ($dbentry['value'] == 1 || $dbentry['value'] == 2) {
                                $already_yes[$editentry['choices'][$i]] -= 1;
                            }

                            if (
                                $already_yes[$editentry['choices'][$i]] >= $max_entries[$editentry['choices'][$i]] &&
                                $max_entries[$editentry['choices'][$i]] != 0
                            ) {
                                $editentry['values'][$i] = '0';
                            }
                        }

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
                    if ($poll->emailentry && !empty($poll->email)) {
                        $this->sendEntryEmail($pollid, $poll->email, $poll->title, $dbuser->id, $dbuser->name, true);
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

    private function isValidEntry(string $pollid, array $newentry, ?int $userid = null): bool
    {
        $isValid = true;

        // Check, that values are in the expected range and had not been manipulated
        $newEntryNumValues = count($newentry['values']);
        for ($i = 0; $i < $newEntryNumValues; $i++) {
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
        $validchoices = [];
        foreach ($dbchoices as $dbc) {
            $validchoices[] = $dbc['id'];
        }
        $dbchoices = array_diff($validchoices, $newentry['choices']);
        if (count($dbchoices) > 0) {
            $isValid = false;
        }

        // Check for editing an entry if user belongs to poll
        if (isset($userid)) {
            $query = $this->Entries->find(
                'all',
                contain: ['Choices'],
                conditions: ['poll_id' => $pollid, 'user_id' => $userid],
            );
            if ($query->count() != count($validchoices)) {
                $isValid = false;
            }
        }

        return $isValid;
    }

    //------------------------------------------------------------------------

    private function isNameAlreadyUsed(string $pollid, string $username): bool
    {
        $query = $this->Entries->find(
            'all',
            contain: ['Users', 'Choices'],
            conditions: ['poll_id' => $pollid, 'Users.name' => $username],
        );
        $number = $query->count();

        return !($number == 0);
    }

    //------------------------------------------------------------------------

    private function getNumOfYesEntries(string $pollid): array
    {
        $dbentries = $this->Entries->find()
            ->where(['poll_id' => $pollid])
            ->contain(['Choices'])
            ->contain(['Users'])
            ->select(['choice_id', 'value']);

        $yesentries = [];
        foreach ($dbentries as $entry) {
            if (!isset($yesentries[$entry->choice_id])) {
                $yesentries[$entry->choice_id] = 0;
            }
            if ($entry->value == 1 || $entry->value == 2) {
                $yesentries[$entry->choice_id] += 1;
            }
        }

        return $yesentries;
    }

    //------------------------------------------------------------------------

    private function sendEntryEmail(
        string $pollid,
        string $email,
        string $title,
        int $userid,
        string $username,
        bool $changedentry = false,
    ): void {
        $dbentries = $this->Entries->find()
            ->where(['poll_id' => $pollid, 'user_id' => $userid])
            ->contain(['Choices' => ['sort' => ['Choices.sort' => 'ASC']]])
            ->select(['Choices.option', 'value']);
        $dbentries = $dbentries->toArray();

        $link = $this->request->scheme() . '://' . $this->request->domain() .
            $this->request->getAttributes()['webroot'] . 'polls/' . $pollid;
        Configure::load('app_local');
        $from = Configure::read('Email.default.from');
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
                ],
            )
            ->deliver();
    }
}
