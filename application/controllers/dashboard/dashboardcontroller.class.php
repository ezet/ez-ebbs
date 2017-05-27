<?php

/**
 * @author     Lars Kristian Dahl <http://www.krisd.com>
 * @copyright  Copyright (c) 2011 Lars Kristian Dahl <http://www.krisd.com>
 * @license    http://www.apache.org/licenses/LICENSE-2.0
 * @version    SVN: $Id$
 */

namespace ebbs\controllers\dashboard;

use ebbs\libraries as lib;
use ebbs\models\dao\BaseDAO;
use ebbs\models\BaseModel;
use ebbs\views\View;

/**
 * Description of Dashboardcontroller
 *
 * @author  Lars Kristian Dahl <http://www.krisd.com>
 */
class Dashboardcontroller extends \ebbs\controllers\ActionController {

    protected $_pagetitle = 'Dashboard';
    protected $_userid;

    /**
     * Called before every dispatch to this controller
     */
    public function preDispatch() {
        // require login for all actions in this controller, and store the userid
        $this->_userid = $this->requireLogin();
    }

    /**
     * Called after every dispatch to this controller
     */
    public function postDispatch() {

    }

    /**
     * This is the default method routed to by the frontcontroller
     */
    public function actionIndex() {
        $this->setView('dashboard/dashboard');
        $this->pagetitle = 'Dashboard';
        $this->content->webuser = $this->_user;
        $this->content->user = BaseDAO::factory('User')->findById($this->_userid);
    }

    public function actionEdit($userid) {
        $this->_check($userid);
        $dao = BaseDAO::factory('User');
        // populate the form
        $user = $dao->findById($userid);
        // if form is posted and valid
        if ($this->_validForm($this->_req->getPost('form'), $user)) {
            // prepare for persistence, update and redirect
            $user->prepare();
            $dao->update($user);
            $this->_redirect('dashboard');
        }
        // prepare the view
        $this->setView('user/editprofile');
        $this->pagetitle = 'Edit profile';
        $this->content->user = $user;
    }

    public function actionNewPost() {
        $userid = $this->_user->getId();
        $blogpost = BaseModel::factory('blogpost');
        // if form is posted and valid
        if ($this->_validForm($this->_req->getPost('form'), $blogpost)) {
            // prepare for persistence, insert and redirect
            $blogpost->UserId = $userid;
            $blogpost->prepare();
            $blogpostid = BaseDAO::factory('Blogpost')->insert($blogpost);
            $this->_redirect('blog/viewpost/' . $blogpostid);
        }
        // prepare the view
        $this->setView('blog/editor');
        $this->pagetitle = 'Add new blogpost';
        $this->content->blogpost = $blogpost;
    }

    public function actionEditPost($postid) {
        $this->_check($postid);
        // require the user to be the owner of the post
        $userid = $this->requireOwner('Blogpost', $postid);
        $dao = BaseDAO::factory('Blogpost');
        // pre populate the form
        $blogpost = $dao->FindById($postid);
        // if form is posted and valid
        if ($this->_validform($this->_req->getPost('form'), $blogpost)) {
            // prepare, insert and redirect
            $blogpost->prepare();
            BaseDAO::factory('Blogpost')->update($blogpost);
            $this->_redirect('blog/viewpost/' . $postid);
        }
        // prepare the view
        $this->setView('blog/editor');
        $this->content->pagetitle = 'Edit blogpost';
        $this->content->blogpost = $blogpost;
    }

    public function actionDeletePost($postid) {
        $this->_check($postid);
        // require user to be logged in as owner
        $userid = $this->requireOwner('Blogpost', $postid);
        // delete and redirect
        BaseDAO::factory('Blogpost')->delete($postid);
        $this->_redirect('blog/view/' . $userid);
    }

    /**
     * Log a user out
     */
    public function actionLogout() {
        $this->_user->logout();
        // redirect to index
        $this->_redirect('');
    }

}