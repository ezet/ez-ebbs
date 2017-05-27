<?php

/**
 * @author     Lars Kristian Dahl <http://www.krisd.com>
 * @copyright  Copyright (c) 2011 Lars Kristian Dahl <http://www.krisd.com>
 * @license    http://www.apache.org/licenses/LICENSE-2.0
 * @version    SVN: $Id$
 */

namespace ebbs\controllers\comment;

use ebbs\models\dao\BaseDAO;
use ebbs\models\BaseModel;

/**
 * Description of Commentcontroller
 *
 * @author  Lars Kristian Dahl <http://www.krisd.com>
 */
class CommentController extends \ebbs\controllers\ActionController {

    /**
     * Called before dispatch
     */
    public function preDispatch() {

    }

    /**
     * Called after dispatch
     */
    public function postDispatch() {
        
    }

    /**
     * Default action
     */
    public function actionIndex() {
        // Disallow, returns a 404
        $this->_check(null);
    }

    /**
     * Add a new comment to the specified postid
     * @param <type> $postid
     */
    public function actionAdd($postid) {
        // FIXME Needs forward function! Has been moved to Blogcontroller temporarily
        $this->_check($postid);
        $comment = BaseModel::factory('comment');
        if ($this->_validForm($this->_req->getPost('form'), $comment)) {
            $this->_prepareAdd($comment);
            BaseDAO::factory('Comment')->insert($comment);
            $this->_redirect('blog/viewpost/' . $postid);
        }
    }

    /**
     * Deletes a comment, sorta
     * @param <type> $commentid
     */
    public function actionDelete($commentid) {
        $this->_check($commentid);
        // get the blogpostowners id
        $ownerid = current($this->_req->getParams());
        $this->_check($ownerid);
        // require user to be the blogposts owner
        $this->requireOwner('Blogpost', $ownerid);
        BaseDAO::factory('Comment')->fakedelete($commentid, 'Deleted by blog owner.');
        // return to last page
        $this->_return();
    }

    /**
     * Flags a comment
     * @param <type> $commentid
     */
    public function actionFlag($commentid) {
        $this->_check($commentid);
        $dao = BaseDAO::factory('Comment')->flag($commentid);
        // return to last page
        $this->_return();
    }

}