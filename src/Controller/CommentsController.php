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

use Cake\Core\Configure;
use Cake\Mailer\Mailer;

class CommentsController extends AppController
{
    public function add(?string $pollid = null): object
    {
        if ($this->request->is('post') && isset($pollid) && !empty($pollid)) {
            $poll = $this->fetchTable('Polls')
                ->findById($pollid)
                ->select(['id', 'title', 'locked', 'email', 'emailcomment', 'comment'])
                ->firstOrFail();

            if ((!Configure::read('preferendum.alwaysAllowComments') && !$poll->comment) || $poll->locked) {
                return $this->redirect(['controller' => 'Polls', 'action' => 'view', $pollid]);
            }

            $newcomment = $this->Comments->newEmptyEntity();
            $newcomment = $this->Comments->patchEntity($newcomment, $this->request->getData());
            $newcomment->poll_id = $poll->id;

            if ($this->Comments->save($newcomment)) {
                if ($poll->emailcomment && !empty($poll->email)) {
                    $this->sendCommentEmail($poll->email, $poll->title, $newcomment);
                }

                $this->Flash->success(__('The comment has been saved.'));

                return $this->redirect(['controller' => 'Polls', 'action' => 'view', $poll->id]);
            }
        }
        $this->Flash->error(__('Unable to save your comment.'));

        return $this->redirect(['controller' => 'Polls', 'action' => 'view', $pollid]);
    }

    //------------------------------------------------------------------------

    public function delete(?string $pollid = null, ?string $adminid = null, ?int $comid = null): object
    {
        $this->request->allowMethod(['post', 'delete']);

        if (
            isset($pollid) && !empty($pollid)
            && isset($adminid) && !empty($adminid)
            && isset($comid) && !empty($comid)
        ) {
            $poll = $this->fetchTable('Polls')->findById($pollid)->select('adminid')->firstOrFail();
            $comment = $this->Comments->get($comid);

            if (strcmp($poll['adminid'], $adminid) == 0) {
                if ($this->Comments->delete($comment)) {
                    $this->Flash->success(__('Comment has been deleted.'));

                    return $this->redirect(['controller' => 'Polls', 'action' => 'edit', $pollid, $adminid]);
                }
            }
        }
        $this->Flash->error(__('Comment {0} has NOT been deleted!', $comid));

        return $this->redirect($this->referer());
    }

    //------------------------------------------------------------------------

    private function sendCommentEmail(string $email, string $title, object $comment): void
    {
        $link = $this->request->scheme() . '://' .
            $this->request->domain() . $this->request->getAttributes()['webroot'] . 'polls/' . $comment->poll_id;
        Configure::load('app_local');
        $from = Configure::read('Email.default.from');

        $mailer = new Mailer('default');
        $mailer->viewBuilder()->setTemplate('new_comment')->setLayout('default');
        $mailer->setFrom($from)
            ->setTo($email)
            ->setEmailFormat('text')
            ->setSubject(__('New comment in poll "{0}"', h($title)))
            ->setViewVars(
                [
                    'title' => $title,
                    'link' => $link,
                    'comment' => $comment,
                ]
            )
            ->deliver();
    }
}
