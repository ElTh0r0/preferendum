<?php
/**
 * Sprudel-ng (https://github.com/ElTh0r0/sprudel-ng)
 * Copyright (c) github.com/ElTh0r0
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

class CommentsController extends AppController
{
    public function new()
    {
        if ($this->request->is('post')) {
            $comment = $this->Comments->newEmptyEntity();
            $comment = $this->Comments->patchEntity($comment, $this->request->getData());
            $pollid = $this->request->getData()['pollid'];
            $this->loadModel('Polls');
            $db = $this->Polls->findByPollid($pollid)->select('locked')->firstOrFail();
            $dblocked = $db['locked'];
            
            if ($dblocked == 0) {
                if ($this->Comments->save($comment)) {
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
            $db = $this->Polls->findByPollid($pollid)->select('adminid')->firstOrFail();
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
