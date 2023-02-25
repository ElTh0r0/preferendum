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
 * @version   0.5.0
 */

declare(strict_types=1);

namespace App\Controller;

use Cake\Mailer\Mailer;

class CommentsController extends AppController
{
    public function add($pollid = null)
    {
        if ($this->request->is('post') && isset($pollid) && !empty($pollid)) {
            $poll = $this->fetchTable('Polls')
                ->findById($pollid)
                ->select(['id', 'title', 'locked', 'email', 'emailcomment', 'comment'])
                ->firstOrFail();
            $dbtitle = $poll['title'];
            $dblocked = $poll['locked'];
            $dbemail = $poll['email'];
            $dbemailcomment = $poll['emailcomment'];
            $dbcomment = $poll['comment'];

            if ((!\Cake\Core\Configure::read('preferendum.alwaysAllowComments') && !($dbcomment)) || $dblocked) {
                return $this->redirect(['controller' => 'Polls', 'action' => 'view', $pollid]);
            }

            $comment = $this->Comments->newEmptyEntity();
            $comment = $this->Comments->patchEntity($comment, $this->request->getData());
            $comment->poll_id = $poll['id'];

            if ($this->Comments->save($comment)) {
                if ($dbemailcomment && !empty($dbemail)) {
                    $this->sendCommentEmail($pollid, $dbemail, $dbtitle, $comment);
                }

                $this->Flash->success(__('The comment has been saved.'));
                return $this->redirect(['controller' => 'Polls', 'action' => 'view', $pollid]);
            }
        }
        $this->Flash->error(__('Unable to save your comment.'));
        return $this->redirect(['controller' => 'Polls', 'action' => 'view', $pollid]);
    }

    //------------------------------------------------------------------------

    public function delete($pollid = null, $adminid = null, $comid = null)
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

    private function sendCommentEmail($pollid, $email, $title, $comment)
    {
        $link = $this->request->scheme() . '://' . $this->request->domain() . $this->request->getAttributes()['webroot'] . 'polls/' . $pollid;
        \Cake\Core\Configure::load('app_local');
        $from = \Cake\Core\Configure::read('Email.default.from');

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
