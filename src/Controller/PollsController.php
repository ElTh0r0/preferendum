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
use Cake\I18n\Date;
use Cake\I18n\DateTime;
use Cake\Mailer\Mailer;

class PollsController extends AppController
{
    private const string CSV_SEPARATOR = ',';

    public function initialize(): void
    {
        parent::initialize();

        // Show warning on main page if DB setup script still exists
        $base = $this->request->getUri()->getPath();
        if (
            $base == '/' &&
            file_exists(APP . 'Controller/DbController.php')
        ) {
            $this->Flash->error(__('File "src/Controller/DbController.php" should be removed!'));
        }
    }

    //------------------------------------------------------------------------

    public function add(): ?object
    {
        if ($this->isPollCreationRestriced()) {
            return $this->redirect(['controller' => 'Admin', 'action' => 'login']);
        }
        if (Configure::read('preferendum.demoMode')) {
            $this->Flash->default(__('DEMO mode is enabled - some features may be limited!'), [
                'params' => [
                    'permanent' => true,
                ],
            ]);
        }

        $newpoll = $this->Polls->newEmptyEntity();
        if ($this->request->is('post') && $this->request->getData('choices') !== null) {
            $newpoll = $this->Polls->patchEntity($newpoll, $this->request->getData());

            // Some checks to prevent manipulating disabled input fields through browser tools
            if (Configure::read('preferendum.alwaysUseAdminLinks')) {
                $newpoll->adminid = true;
            }
            if (!$newpoll->adminid) {
                $newpoll->hidevotes = 0;
                $newpoll->editentry = 0;
                $newpoll->userinfo = 0;
            }
            if ($newpoll->anonymous) {
                $newpoll->userinfo = 0;
            }
            if ($newpoll->userinfo) {
                $newpoll->anonymous = 0;
            }

            $pollpw = '';
            if ($newpoll->pwprotect && isset($this->request->getData()['password'])) {
                $pollpw = trim($this->request->getData()['password']);
            }
            if (strcmp($pollpw, '') == 0) {
                $newpoll->pwprotect = 0;
            }

            if (Configure::read('preferendum.demoMode')) {
                $newpoll->hasexp = true;
                $newpoll->expiry = new Date('NOW +1 day');
            }

            // Temporary save email in separate variable before calling validateSettings(), if poll links shall be sent
            // (If entry/comments shall not be sent, email won't be saved!)
            $pollemail = '';
            if (
                filter_var($newpoll->email, FILTER_VALIDATE_EMAIL) &&
                $newpoll->emailpoll &&
                Configure::read('preferendum.opt_SendPollCreationEmail')
            ) {
                $pollemail = $newpoll->email;
            }
            $this->validateSettings($newpoll); // Call by reference

            if ($this->Polls->save($newpoll)) {
                $success = true;
                $choices = $this->request->getData('choices');
                $max_entries = [];
                if (
                    $newpoll->limitentry &&
                    Configure::read('preferendum.opt_MaxEntriesPerOption')
                ) {
                    $max_entries = $this->request->getData('max_entries');
                }
                $numChoices = count($choices);
                for ($i = 0; $i < $numChoices; $i++) {
                    $dbchoice = $this->fetchTable('Choices')->newEmptyEntity();
                    $dbchoice = $this->fetchTable('Choices')->newEntity(
                        [
                            'poll_id' => $newpoll->id,
                            'option' => trim($choices[$i]),
                            'sort' => $i + 1,
                        ],
                    );
                    if (count($choices) == count($max_entries)) {
                        if (is_numeric($max_entries[$i])) {
                            $dbchoice['max_entries'] = $max_entries[$i];
                        } else {
                            $dbchoice['max_entries'] = 0;
                        }
                    }
                    if (!$this->fetchTable('Choices')->save($dbchoice)) {
                        $success = false;
                        break;
                    }
                }

                if ($success && $newpoll->pwprotect) {
                    $success = $this->createPollPwUser($newpoll->id, $pollpw);
                }

                if ($success) {
                    if ($newpoll->pwprotect) {
                        $this->Flash->default(
                            __('Your poll has been saved.') . '<br><br>' .
                                __('Please login with your chosen password!'),
                            [
                                'params' => [
                                    'class' => 'success',
                                    'permanent' => true,
                                    'escape' => false,
                                ],
                            ],
                        );
                    } else {
                        $this->Flash->success(__('Your poll has been saved.'));
                    }

                    if (!empty($pollemail)) {
                        $this->sendPollEmail($pollemail, $newpoll->title, $newpoll->id, $newpoll->adminid, $pollpw);
                    }

                    if ($newpoll->adminid == true) {
                        return $this->redirect(['action' => 'view', $newpoll->id, $newpoll->adminid]);
                    }

                    return $this->redirect(['action' => 'view', $newpoll->id]);
                } else {
                    $this->Polls->delete($newpoll);
                }
            }
            $this->Flash->error(__('Unable to add your poll.'));
        }

        $this->set('poll', $newpoll);

        return null;
    }

    //------------------------------------------------------------------------

    public function view(?string $pollid = null, string $adminid = 'NA', ?string $userpw = null): ?object
    {
        $this->checkExpiryAndLock($pollid);

        $poll = $this->getPollAndComments($pollid);
        if ($this->isPollAccessRestriced($poll->id, $poll->pwprotect)) {
            return $this->redirect(['controller' => 'Admin', 'action' => 'login', $poll->id, $adminid]);
        }
        $pollchoices = $this->getPollChoices($pollid);
        $dbentries = $this->getDbEntries($pollid);

        $pollentries = [];
        $usermap = [];
        $usermap_pw = [];
        $usermap_info = [];
        // TODO: Think about better implementation for passing all the data...
        foreach ($dbentries as $entry) {
            if (!isset($pollentries[$entry['name']])) {
                $pollentries[$entry['name']] = [];
                $usermap[$entry['name']] = $entry->user_id;
                $usermap_pw[$entry['name']] = $entry->user_pw;
                $usermap_info[$entry['name']] = $entry->user_info;
            }
            $pollentries[$entry->name][$entry->choice_id] = $entry->value;
        }

        if ($poll->locked != 0) {
            $this->Flash->default(__('This poll is locked - it is not possible to insert new entries or comments!'), [
                'params' => [
                    'class' => 'error',
                    'permanent' => true,
                ],
            ]);
        }
        if ($poll->hidevotes != 0) {
            $this->Flash->default(__('Only poll admin can see votes and comments!'), [
                'params' => [
                    'permanent' => true,
                ],
            ]);
        }

        // Check if valid user password was provided for editing an entry and if editing is allowed at all
        if (isset($userpw) && ($poll->editentry != 0)) {
            if (!in_array($userpw, $usermap_pw)) {
                $userpw = null;
                $usermap = [];
                $usermap_pw = [];
                $usermap_info = [];
            }
        } else {
            $userpw = null;
            $usermap = [];
            $usermap_pw = [];
            $usermap_info = [];
        }

        $newentry = $this->fetchTable('Entries')->newEmptyEntity(); // New empty entity for new entry
        $newcomment = $this->fetchTable('Comments')->newEmptyEntity(); // New empty entity for new comment

        $this->set(compact(
            'poll',
            'adminid',
            'pollchoices',
            'pollentries',
            'usermap',
            'usermap_pw',
            'userpw',
            'usermap_info',
            'newentry',
            'newcomment',
        ));

        return null;
    }

    //------------------------------------------------------------------------

    public function edit(?string $pollid = null, string $adminid = 'NA', string $userpw = ''): ?object
    {
        $this->checkExpiryAndLock($pollid);
        if (Configure::read('preferendum.demoMode')) {
            $this->Flash->default(__('DEMO mode is enabled - some features may be limited!'), [
                'params' => [
                    'permanent' => true,
                ],
            ]);
        }

        $poll = $this->getPollAndComments($pollid);
        if (!strcmp($poll->adminid, $adminid) == 0) {
            return $this->redirect(['action' => 'view', $pollid]);
        }
        if ($this->isPollAccessRestriced($poll->id, $poll->pwprotect)) {
            return $this->redirect(['controller' => 'Admin', 'action' => 'login', $poll->id, $adminid]);
        }

        $pollchoices = $this->getPollChoices($pollid);
        $dbentries = $this->getDbEntries($pollid);

        $pollentries = [];
        $usermap = [];
        $usermap_pw = [];
        $usermap_info = [];
        // TODO: Think about better implementation for passing all the data...
        foreach ($dbentries as $entry) {
            if (!isset($pollentries[$entry['name']])) {
                $pollentries[$entry['name']] = [];
                $usermap[$entry['name']] = $entry->user_id;
                $usermap_pw[$entry['name']] = $entry->user_pw;
                $usermap_info[$entry['name']] = $entry->user_info;
            }
            $pollentries[$entry->name][$entry->choice_id] = $entry->value;
        }

        if ($poll->locked != 0) {
            $this->Flash->default(__('This poll is locked!'), [
                'params' => [
                    'class' => 'error',
                    'permanent' => true,
                ],
            ]);
        }

        $newchoice = $this->fetchTable('Choices')->newEmptyEntity(); // Needed for adding new options
        $newentry = $this->fetchTable('Entries')->newEmptyEntity(); // New empty entity for new entry

        $this->set(compact(
            'poll',
            'adminid',
            'pollchoices',
            'pollentries',
            'usermap',
            'usermap_pw',
            'userpw',
            'usermap_info',
            'newchoice',
            'newentry',
        ));

        return null;
    }

    //------------------------------------------------------------------------

    public function update(?string $pollid = null, ?string $adminid = null): ?object
    {
        $this->request->allowMethod(['post', 'update']);

        if (
            $this->request->is('post', 'put') &&
            isset($pollid) && !empty($pollid) &&
            isset($adminid) && !empty($adminid)
        ) {
            $poll = $this->Polls->findById($pollid)->firstOrFail();
            $wasPwProtected = $poll->pwprotect;
            $dbadminid = $poll->adminid;
            if (strcmp($dbadminid, $adminid) == 0) {
                $pollexp = $poll->expiry; // Store temporary to prevent manipulation, if demo mode is enabled
                $this->Polls->patchEntity($poll, $this->request->getData());
                if (Configure::read('preferendum.demoMode')) {
                    $poll->expiry = $pollexp;
                }

                $pollpw = '';
                if ($poll->pwprotect && isset($this->request->getData()['password'])) {
                    $pollpw = trim($this->request->getData()['password']);
                }

                $this->validateSettings($poll); // Call by reference

                if ($this->Polls->save($poll)) {
                    $success = true;
                    // Update password user
                    if ($wasPwProtected && $poll->pwprotect && (strcmp($pollpw, '') != 0)) {
                        $dbpwuser = $this->fetchTable('Users')->find()
                            ->select('id')
                            ->where(['name' => $poll->id, 'role' => self::POLLPWROLE])
                            ->firstOrFail();
                        $dbpwuser->password = (new DefaultPasswordHasher())->hash(trim($pollpw));
                        $success = $this->fetchTable('Users')->save($dbpwuser);
                    }

                    // Delete password user, if password protection was removed
                    if ($wasPwProtected && !$poll->pwprotect) {
                        $success = $this->deletePollPwUser($poll->id);
                    }

                    // Create password user, if password protection was added
                    if (!$wasPwProtected && $poll->pwprotect) {
                        $success = $this->createPollPwUser($poll->id, $pollpw);
                    }

                    if ($success) {
                        $this->Flash->success(__('Your poll has been updated.'));

                        return $this->redirect(['action' => 'edit', $poll->id, $adminid]);
                    }
                }
                $this->Flash->error(__('Unable to update your poll.'));
            } else {
                return $this->redirect(['action' => 'index']);
            }
        }

        return null;
    }

    //------------------------------------------------------------------------

    public function togglelock(?string $pollid = null, ?string $adminid = null): object
    {
        $this->request->allowMethod(['post', 'togglelock']);

        if (
            isset($pollid) && !empty($pollid)
            && isset($adminid) && !empty($adminid)
        ) {
            $poll = $this->Polls->findById($pollid)->firstOrFail();
            $dbadminid = $poll->adminid;

            if (strcmp($dbadminid, $adminid) == 0) {
                if ($poll->locked == 0) {
                    $poll->locked = 1;
                } else {
                    $poll->locked = 0;
                }

                if ($this->Polls->save($poll)) {
                    if ($poll->locked == 0) {
                        $this->Flash->success(__('Poll has been unlocked'));
                    }

                    return $this->redirect(['action' => 'edit', $pollid, $adminid]);
                }
            }
        }
        $this->Flash->error(__('Poll lock has NOT been changed!', $pollid));

        return $this->redirect($this->referer());
    }

    //------------------------------------------------------------------------

    public function sendpersonallink(): object
    {
        $this->request->allowMethod(['post', 'sendpersonallink']);

        if ($this->request->is('post')) {
            $data = $this->request->getData();
            // debug($data);
            // die;

            if (isset($data) && !empty($data)) {
                $pollname = $data['pollname'];
                $name = $data['name'];
                $email = $data['email'];
                $link = $data['link'];
                if (
                    filter_var($email, FILTER_VALIDATE_EMAIL) &&
                    isset($pollname) && !empty($pollname) &&
                    isset($name) && !empty($name) &&
                    isset($link) && !empty($link)
                ) {
                    Configure::load('app_local');
                    $from = Configure::read('Email.default.from');
                    $mailer = new Mailer('default');

                    $subject = __('Your personal link for poll "{0}"', h($pollname));
                    $mailer->viewBuilder()->setTemplate('personal_link')->setLayout('default');
                    $mailer->setFrom($from)
                        ->setTo($email)
                        ->setEmailFormat('text')
                        ->setSubject($subject)
                        ->setViewVars(
                            [
                                'pollname' => $pollname,
                                'name' => $name,
                                'link' => $link,
                            ],
                        )
                        ->deliver();

                    $this->Flash->success(__('Email with personal link was sent to "{0}"', $email));

                    return $this->redirect($this->referer());
                }
            }
        }
        $this->Flash->error(__('Email could not be sent!'));

        return $this->redirect($this->referer());
    }

    //------------------------------------------------------------------------

    public function exportcsv(?string $pollid = null, ?string $adminid = null): void
    {
        $this->request->allowMethod(['post', 'exportcsv']);

        if (
            isset($pollid) && !empty($pollid)
            && isset($adminid) && !empty($adminid)
        ) {
            $poll = $this->Polls->findById($pollid)->firstOrFail();
            $dbadminid = $poll->adminid;

            if (strcmp($dbadminid, $adminid) == 0) {
                $filename = $poll->title . '.csv';
                $filename = str_replace(' ', '_', $filename);
                $choices = $this->getPollChoices($pollid);
                $headerline = array_column($choices, 'option');

                if ($poll->limitentry) {
                    $maxentries = array_column($choices, 'max_entries');
                    if (count($headerline) == count($maxentries)) {
                        $numHeaders = count($headerline);
                        for ($i = 0; $i < $numHeaders; $i++) {
                            if ($maxentries[$i] > 0) {
                                $headerline[$i] .= __(' - {0} pers.', $maxentries[$i]);
                            }
                        }
                    }
                }

                if ($poll->userinfo) {
                    array_unshift($headerline, __('Contact info'));
                }
                array_unshift($headerline, __('Name'));

                ob_start();
                $fp = fopen('php://output', 'w');
                fputcsv($fp, $headerline, self::CSV_SEPARATOR);

                $dbentries = $this->getDbEntries($pollid);
                $pollentries = [];
                $usermap_info = [];
                // TODO: Think about better implementation for passing all the data...
                foreach ($dbentries as $entry) {
                    if (!isset($pollentries[$entry['name']])) {
                        $pollentries[$entry['name']] = [];
                        $usermap_info[$entry['name']] = $entry->user_info;
                    }
                    $pollentries[$entry->name][$entry->choice_id] = $entry->value;
                }
                foreach ($pollentries as $name => $entry) {
                    $csvline = [];
                    $csvline[] = $name;
                    if ($poll->userinfo) {
                        $csvline[] = $usermap_info[$name];
                    }

                    $numChoises = count($choices);
                    for ($i = 0; $i < $numChoises; $i++) {
                        switch ($entry[$choices[$i]->id]) {
                            case 0:
                                $csvline[] = __('no');
                                break;
                            case 1:
                                $csvline[] = __('yes');
                                break;
                            default:
                                $csvline[] = __('maybe');
                        }
                    }
                    fputcsv($fp, $csvline);
                }

                fclose($fp);

                header('Content-Type: text/csv; charset=utf-8');
                header('Content-Disposition: attachment; filename=' . $filename);
                ob_end_flush();
            }
        }
        exit;
    }

    //------------------------------------------------------------------------

    public function delete(?string $pollid = null, ?string $adminid = null): object
    {
        $this->request->allowMethod(['post', 'delete']);

        if (
            isset($pollid) && !empty($pollid)
            && isset($adminid) && !empty($adminid)
        ) {
            $poll = $this->Polls->findById($pollid)->firstOrFail();
            $dbadminid = $poll->adminid;
            if (strcmp($dbadminid, $adminid) == 0) {
                if ($this->deleteSinglePoll($poll)) {
                    $this->Flash->success(__('Poll {0} has been deleted.', $poll->title));
                    if (str_starts_with($this->referer(), '/admin')) {
                        // Stay on admin page, if deletion was triggered from there
                        return $this->redirect($this->referer());
                    }

                    return $this->redirect(['action' => 'index']);
                }
            }
        }
        $this->Flash->error(__('Poll {0} has NOT been deleted!', $pollid));

        return $this->redirect($this->referer());
    }

    //------------------------------------------------------------------------

    public function cleanup(): void
    {
        if (!(PHP_SAPI === 'cli')) {
            echo 'ERROR: The cleanup routine can only be run from the command line (i.e. via cronjob).' . PHP_EOL;
            exit;
        }

        echo PHP_EOL;
        echo 'PREFERendum cleanup routine' . PHP_EOL;
        echo '***************************' . PHP_EOL;
        echo PHP_EOL;
        echo 'You may change the cleanup values in "config/preferendum_features.php"' . PHP_EOL;
        $this->cleanupInactivePolls(true);
        $this->cleanupExpiredPolls(true);
        echo 'Done.' . PHP_EOL;

        $this->autoRender = false;
    }

    //------------------------------------------------------------------------

    public function cleanupmanually(bool $deleteexpired): object
    {
        $this->loadComponent('Authentication.Authentication');
        $result = $this->Authentication->getResult();
        if ($result->isValid()) {
            $adminRole = self::BACKENDROLES[0];
            $polladmRole = self::BACKENDROLES[1];
            $identity = $this->Authentication->getIdentity();
            $currentUserRole = $identity->getOriginalData()['role'];

            if (
                in_array($currentUserRole, self::BACKENDROLES) &&
                (strcmp($currentUserRole, $adminRole) == 0 ||
                    strcmp($currentUserRole, $polladmRole) == 0)
            ) {
                if ($deleteexpired) {
                    $this->cleanupExpiredPolls();
                } else {
                    $this->cleanupInactivePolls();
                }
            } else {
                $this->Authentication->logout();
            }
        }

        return $this->redirect($this->referer());
    }

    //------------------------------------------------------------------------

    private function cleanupInactivePolls(bool $cli = false): void
    {
        $rmInactiveAfter = Configure::read('preferendum.deleteInactivePollsAfter');
        if ($rmInactiveAfter <= 0) {
            return;
        }

        //minimum last changed date
        $minDateInactive = date('Y-m-d H:i:s', strtotime('-' . $rmInactiveAfter . ' days', time()));

        if ($cli) {
            echo PHP_EOL;
            echo 'This will delete every poll that was inactive' . PHP_EOL . 'since ' . $minDateInactive .
                ' (for at least ' . $rmInactiveAfter . ' days):' . PHP_EOL;
        }
        $trash = $this->Polls->find('all')->where(['modified <' => $minDateInactive]);
        $trash = $trash->all()->toArray();

        //remove old polls
        if (count($trash) > 0) {
            foreach ($trash as $poll) {
                if ($this->deleteSinglePoll($poll, $cli)) {
                    if (!$cli) {
                        $this->Flash->success(__('Inactive poll {0} has been deleted.', $poll->title));
                    }
                } else {
                    if (!$cli) {
                        $this->Flash->error(__('Inactive poll {0} could NOT be deleted!', $poll->title));
                    }
                }
            }
        }

        if ($cli) {
            echo 'Deleted ' . count($trash) . ' inactive polls.' . PHP_EOL;
            echo PHP_EOL;
        }
    }

    //------------------------------------------------------------------------

    private function cleanupExpiredPolls(bool $cli = false): void
    {
        $rmExpiredAfter = Configure::read('preferendum.deleteExpiredPollsAfter');
        if ($rmExpiredAfter <= 0) {
            return;
        }

        // Minimum expiration date
        $minDateExpired = date('Y-m-d H:i:s', strtotime('-' . $rmExpiredAfter . ' days', time()));

        if ($cli) {
            echo PHP_EOL;
            echo 'This will delete every poll that has expired' . PHP_EOL . 'since ' . $minDateExpired .
                ' (for at least ' . $rmExpiredAfter . ' days):' . PHP_EOL;
        }
        $trash = $this->Polls->find('all')->where([
            'expiry IS NOT' => null,
            'expiry <' => $minDateExpired,
        ]);
        $trash = $trash->all()->toArray();

        // Remove old polls
        if (count($trash) > 0) {
            foreach ($trash as $poll) {
                if ($this->deleteSinglePoll($poll, $cli)) {
                    if (!$cli) {
                        $this->Flash->success(__('Expired poll {0} has been deleted.', $poll->title));
                    }
                } else {
                    if (!$cli) {
                        $this->Flash->error(__('Expired poll {0} could NOT be deleted!', $poll->title));
                    }
                }
            }
        }

        if ($cli) {
            echo 'Deleted ' . count($trash) . ' expired polls.' . PHP_EOL;
            echo PHP_EOL;
        }
    }

    //------------------------------------------------------------------------

    private function deleteSinglePoll(object $poll, bool $cli = false): bool
    {
        if ($cli) {
            echo ' - Deleting poll "' . $poll->title . '" (' . $poll->id . ') ...' . PHP_EOL;
        }

        $entries = $this->fetchTable('Entries')->find()
            ->where(['poll_id' => $poll->id])
            ->contain(['Choices'])
            ->select(['id']);
        // Collect users before deleting the entries, otherwise users cannot be found anymore
        $dbusers = $this->fetchTable('Entries')->find()
            ->where(['poll_id' => $poll->id])
            ->contain(['Users', 'Choices'])
            ->select(['user_id' => 'Users.id'])
            ->groupBy(['Users.id'])->all();
        $users = [];
        foreach ($dbusers as $usr) {
            $users[] = $usr['user_id'];
        }

        // Entries must be deleted manually, since there is no direct dependency between Polls and Entries table
        if ($entries->count() > 0) {
            if (!$this->fetchTable('Entries')->deleteAll(['id IN' => $entries])) {
                if ($cli) {
                    echo 'ERROR while deleting: ' . $poll->id . ' (error while Entries deletion)' . PHP_EOL;
                }

                return false;
            }
        }

        // Users must be deleted manually, since there is no direct dependency between Polls and Users table
        if (count($users) > 0) {
            if (!$this->fetchTable('Users')->deleteAll(['id IN' => $users])) {
                if ($cli) {
                    echo 'ERROR while deleting: ' . $poll->id . ' (error while Users deletion)' . PHP_EOL;
                }

                return false;
            }
        }

        // Delete poll password user
        if ($poll->pwprotect) {
            if (!$this->deletePollPwUser($poll->id)) {
                if ($cli) {
                    echo 'ERROR while deleting: ' . $poll->id . ' (error while PollPwUser deletion)' . PHP_EOL;
                }

                return false;
            }
        }

        // Comments and Choices deleted automatically due to table dependency
        if (!$this->Polls->delete($poll)) {
            if ($cli) {
                echo 'ERROR while deleting: ' . $poll->id . ' (error while Poll/Comments/Choices deletion)' . PHP_EOL;
            }

            return false;
        }

        return true;
    }

    //------------------------------------------------------------------------

    private function sendPollEmail(
        string $email,
        string $title,
        string $pollid,
        string $adminid,
        string $password,
    ): void {
        Configure::load('app_local');
        $from = Configure::read('Email.default.from');
        $mailer = new Mailer('default');
        $publiclink = $this->request->scheme() . '://' . $this->request->domain() .
            $this->request->getAttributes()['webroot'] . 'polls/' . $pollid;

        if (strcmp($adminid, 'NA') != 0) {
            $adminlink = $publiclink . '/' . $adminid;
            $subject = __('New poll "{0}" - ADMIN link', h($title));
            $mailer->viewBuilder()->setTemplate('new_poll_admin')->setLayout('default');
            $mailer->setFrom($from)
                ->setTo($email)
                ->setEmailFormat('text')
                ->setSubject($subject)
                ->setViewVars(
                    [
                        'title' => $title,
                        'link' => $adminlink,
                        'password' => $password,
                    ],
                )
                ->deliver();
        }

        $subject = __('New poll "{0}" - USER link', h($title));
        $mailer->viewBuilder()->setTemplate('new_poll_user')->setLayout('default');
        $mailer->setFrom($from)
            ->setTo($email)
            ->setEmailFormat('text')
            ->setSubject($subject)
            ->setViewVars(
                [
                    'title' => $title,
                    'link' => $publiclink,
                    'password' => $password,
                ],
            )
            ->deliver();
    }

    //------------------------------------------------------------------------

    private function isPollCreationRestriced(): bool
    {
        $isRestricted = false;

        if (
            Configure::read('preferendum.adminInterface') &&
            Configure::read('preferendum.restrictPollCreation')
        ) {
            $this->loadComponent('Authentication.Authentication');
            $result = $this->Authentication->getResult();
            if ($result->isValid()) {
                $adminRole = self::BACKENDROLES[0];
                $polladmRole = self::BACKENDROLES[1];
                $identity = $this->Authentication->getIdentity();
                $currentUserRole = $identity->getOriginalData()['role'];

                if (
                    strcmp($currentUserRole, $adminRole) != 0 &&
                    strcmp($currentUserRole, $polladmRole) != 0
                ) {
                    $isRestricted = true;
                    $this->Authentication->logout();
                }
            } else {
                $isRestricted = true;
            }
        }

        return $isRestricted;
    }

    //------------------------------------------------------------------------

    private function isPollAccessRestriced(string $pollid, bool $pwprotect): bool
    {
        $isRestricted = false;

        if ($pwprotect) {
            $this->loadComponent('Authentication.Authentication');
            $result = $this->Authentication->getResult();
            if ($result->isValid()) {
                $pollpwRole = self::POLLPWROLE;
                $identity = $this->Authentication->getIdentity();
                $currentUserName = $identity->getOriginalData()['name'];
                $currentUserRole = $identity->getOriginalData()['role'];

                if (
                    !in_array($currentUserRole, self::BACKENDROLES) &&
                    (strcmp($currentUserName, $pollid) != 0 ||
                        strcmp($currentUserRole, $pollpwRole) != 0)
                ) {
                    $isRestricted = true;
                    $this->Authentication->logout();
                }
            } else {
                $isRestricted = true;
            }
        }

        return $isRestricted;
    }

    //------------------------------------------------------------------------

    private function createPollPwUser(string $pollid, string $pollpw): bool
    {
        $dbpwuser = $this->fetchTable('Users')->newEmptyEntity();
        $dbpwuser = $this->fetchTable('Users')->newEntity(
            [
                'name' => $pollid,
                'role' => self::POLLPWROLE,
                'password' => (new DefaultPasswordHasher())->hash(trim($pollpw)),
            ],
        );

        if ($this->fetchTable('Users')->save($dbpwuser)) {
            return true;
        }

        return false;
    }

    //------------------------------------------------------------------------

    private function deletePollPwUser(string $pollid): bool
    {
        $dbpwuser = $this->fetchTable('Users')->find()
            ->select('id')
            ->where(['name' => $pollid, 'role' => self::POLLPWROLE])
            ->firstOrFail();

        if ($this->fetchTable('Users')->delete($dbpwuser)) {
            return true;
        }

        return false;
    }

    //------------------------------------------------------------------------

    private function getPollAndComments(string $pollid): object
    {
        $poll = $this->Polls->find()
            ->where(['id' => $pollid])
            ->contain(['Comments' => ['sort' => ['Comments.created' => 'DESC']]])
            ->firstOrFail();

        return $poll;
    }

    //------------------------------------------------------------------------

    private function getPollChoices(string $pollid): array
    {
        $dbchoices = $this->fetchTable('Choices')->find()
            ->where(['poll_id' => $pollid])
            ->select(['id', 'option', 'max_entries'])
            ->orderBy(['sort' => 'ASC']);

        return $dbchoices->toArray();
    }

    //------------------------------------------------------------------------

    private function getDbEntries(string $pollid): object
    {
        $dbentries = $this->fetchTable('Entries')->find()
            ->where(['poll_id' => $pollid])
            ->contain(['Choices' => ['sort' => ['Choices.sort' => 'ASC']]])
            ->contain(['Users'])
            ->select([
                'choice_id',
                'value',
                'name' => 'Users.name',
                'user_id' => 'Users.id',
                'user_pw' => 'Users.password',
                'user_info' => 'Users.info',
            ])
            ->orderBy(['user_id' => 'ASC']);

        return $dbentries;
    }

    //------------------------------------------------------------------------

    private function validateSettings(object &$poll): void // Call by reference - $poll changed directly.
    {
        if (
            !Configure::read('preferendum.alwaysAllowComments')
            && !Configure::read('preferendum.opt_Comments')
        ) {
            $poll->comment = 0;
            $poll->emailcomment = 0;
        }
        if (!$poll->comment && Configure::read('preferendum.opt_Comments')) {
            $poll->emailcomment = 0;
        }
        if (!filter_var($poll->email, FILTER_VALIDATE_EMAIL)) {
            $poll->emailentry = 0;
            $poll->emailcomment = 0;
        }
        if (!$poll->emailentry && !$poll->emailcomment) {
            $poll->email = '';
        }
        if (!Configure::read('preferendum.opt_PollPassword')) {
            $poll->pwprotect = 0;
        }
        if (!Configure::read('preferendum.opt_MaxEntriesPerOption')) {
            $poll->limitentry = 0;
        }
        if (
            (Configure::read('preferendum.opt_PollExpirationAfter') == 0 ||
                // $poll->expiry <= DateTime::now() ||
                $poll->hasexp == 0) && !Configure::read('preferendum.demoMode')
        ) {
            $poll->expiry = null;
        }
    }

    //------------------------------------------------------------------------

    private function checkExpiryAndLock(?string $pollid = null): void
    {
        if (Configure::read('preferendum.opt_PollExpirationAfter') > 0) {
            if (isset($pollid) && !empty($pollid)) {
                $expired = $this->Polls->UpdateQuery();
                $expired->set(['locked' => 1])
                    ->where([
                        'id' => $pollid,
                        'locked' => 0,
                        'expiry IS NOT' => null,
                        'expiry <=' => DateTime::now(),
                    ])
                    ->execute();
            }
        }
    }
}
