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
use Cake\Mailer\Mailer;

class CommentsController extends AppController
{
    public function new()
    {
        if ($this->request->is('post')) {
            $comment = $this->Comments->newEmptyEntity();
            $comment = $this->Comments->patchEntity($comment, $this->request->getData());
            $pollid = $this->request->getData()['poll_id'];
            $this->loadModel('Polls');
            $db = $this->Polls->findById($pollid)->select(['title', 'locked', 'email', 'emailcomment'])->firstOrFail();
            $dbtitle = $db['title'];
            $dblocked = $db['locked'];
            $dbemail = $db['email'];
            $dbemailcomment = $db['emailcomment'];
            $link = $this->request->scheme() . '://' . $this->request->domain() . $this->request->getAttributes()['webroot'] . 'polls/' . $pollid;
            \Cake\Core\Configure::load('app_local');
            $from = \Cake\Core\Configure::read('Email.default.from');

            if ($dblocked == 0) {
                if ($this->Comments->save($comment)) {
                    if ($dbemailcomment && !empty($dbemail)) {
                        $mailer = new Mailer('default');
                        $mailer->viewBuilder()->setTemplate('new_comment')->setLayout('default');
                        $mailer->setFrom($from)
                            ->setTo($dbemail)
                            ->setEmailFormat('text')
                            ->setSubject(__('New comment in poll "{0}"', h($dbtitle)))
                            ->setViewVars(
                                [
                                'title' => $dbtitle,
                                'link' => $link,
                                'comment' => $comment,
                                ]
                            )
                            ->deliver();
                    }

                    return $this->redirect(['controller' => 'Polls', 'action' => 'view', $pollid]);
                }
            }
        }
        $this->Flash->error(__('Unable to save your comment.'));
        return $this->redirect(['controller' => 'Polls', 'action' => 'view', $pollid]);
    }

    //------------------------------------------------------------------------

    public function delete($pollid = null, $adminid = null, $comid = null)
    {
        $this->request->allowMethod(['post', 'delete']);

        if (isset($pollid) && !empty($pollid)
            && isset($adminid) && !empty($adminid)
            && isset($comid) && !empty($comid)
        ) {
            $this->loadModel('Polls');
            $db = $this->Polls->findById($pollid)->select('adminid')->firstOrFail();
            $dbadminid = $db['adminid'];

            $dbcomment = $this->Comments->findById($comid)->firstOrFail();
            if (strcmp($dbadminid, $adminid) == 0) {
                if ($this->Comments->delete($dbcomment)) {
                    $this->Flash->success(__('Comment has been deleted.'));
                    return $this->redirect(['controller' => 'Polls', 'action' => 'edit', $pollid, $adminid]);
                }
            }
        }
        $this->Flash->error(__('Comment {0} has NOT been deleted!', $comid));
        return $this->redirect($this->referer());
    }
}
