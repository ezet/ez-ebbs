<?php

/**
 * @author     Lars Kristian Dahl <http://www.krisd.com>
 * @copyright  Copyright (c) 2011 Lars Kristian Dahl <http://www.krisd.com>
 * @license    http://www.apache.org/licenses/LICENSE-2.0
 * @version    SVN: $Id$
 */

namespace ebbs\controllers\admin;

use ebbs\controllers\ActionController;
use ebbs\models\dao\BaseDAO;

/**
 * Description of Admincontroller
 *
 * @author  Lars Kristian Dahl <http://www.krisd.com>
 */
class AdminController extends ActionController {

    /**
     * Called before dispatch
     */
    public function preDispatch() {
        // require admin rights to use this controller
        $this->requireAdmin();
    }

    /**
     * Called after every dispatch
     */
    public function postDispatch() {

    }

    /**
     * Default action
     */
    public function actionIndex() {
        $this->setView('admin/admindash');
    }

    /**
     * View all flagged posts
     */
    public function actionFlaggedPosts() {
        $this->setView('admin/posts');
        $this->content->blogposts = BaseDAO::factory('Blogpost')->findFlagged();
    }

    /**
     * View all flagged comments
     */
    public function actionFlaggedComments() {
        $this->setView('admin/comments');
        $this->content->comments = BaseDAO::factory('Comment')->findFlagged();
    }

    /**
     * Deletes a post, sorta
     * @param <type> $postid
     */
    public function actionDeletePost($postid) {
        $this->_check($postid);
        BaseDAO::factory('Blogpost')->fakedelete($postid, 'This post has been deleted by the Administrator.');
        $this->_return();
    }

    /**
     * Deletes a comment, sorta
     * @param <type> $commentid
     */
    public function actionDeleteComment($commentid) {
        $this->_check($commentid);
        BaseDAO::factory('Comment')->fakedelete($commentid, 'This comment has been deleted by the Administrator.');
        $this->_return();
    }

    /**
     * Deletes a user
     * @param <type> $userid
     */
    public function actionDeleteUser($userid) {
        $this->_check($userid);
        BaseDAO::factory('User')->delete($userid);
        $this->_return();
    }

    /**
     * Unflags a post
     * @param <type> $postid
     */
    public function actionUnflagPost($postid) {
        $this->_check($postid);
        BaseDAO::factory('Blogpost')->unflag($postid);
        $this->_return();
    }

    /**
     * Unflags a comment
     * @param <type> $commentid
     */
    public function actionUnflagComment($commentid) {
        $this->_check($commentid);
        BaseDAO::factory('Comment')->unflag($commentid);
        $this->_return();
    }

    /**
     * Blocks a user
     * @param <type> $userid
     */
    public function actionBlockUser($userid) {
        $this->_check($userid);
        BaseDAO::factory('User')->block($userid);
        $this->_return();
    }

    /**
     * Unblocks a user
     * @param <type> $userid
     */
    public function ActionUnblockUser($userid) {
        $this->_check($userid);
        BaseDAO::factory('User')->unblock($userid);
        $this->_return();
    }

    /**
     * Lists all blocked users
     */
    public function actionBlockedUsers() {
        $this->setView('admin/blockedlist');
        $this->content->users = BaseDAO::factory('User')->findBlocked();
    }

    /**
     * Basic user stats, returns result ordered by request
     * @param <type> $sortby
     */
    public function actionUserStats($sortby) {
        // TODO Improve this function, this one is extremely basic
        $this->setView('admin/userstats');
        $this->content->users = BaseDAO::factory('User')->findAllBy($sortby);
    }

}