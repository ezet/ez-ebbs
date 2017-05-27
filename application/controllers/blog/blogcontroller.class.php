<?php

/**
 * @author     Lars Kristian Dahl <http://www.krisd.com>
 * @copyright  Copyright (c) 2011 Lars Kristian Dahl <http://www.krisd.com>
 * @license    http://www.apache.org/licenses/LICENSE-2.0
 * @version    SVN: $Id$
 */

namespace ebbs\controllers\blog;

use ebbs\models\BaseModel;
use ebbs\models\dao\BaseDAO;
use ebbs\views\View;
use ebbs\libraries\Session;
use ebbs\libraries\AuthException;

/**
 * ActionController for the ebbs Blog module.
 *
 * @author  Lars Kristian Dahl <http://www.krisd.com>
 */
class BlogController extends \ebbs\controllers\ActionController {

    /**
     * This is called before every dispatch to this controller
     */
    public function preDispatch() {
        // set the default style when viewing blogs
        // Using this simple method, you can also change the whole layout of the page
        // See ActionController::$_layout that gets rendered as a view
        $this->style = 'common';
    }

    /**
     * This is called after every dispatch to this controller
     */
    public function postDispatch() {

    }

    /**
     * This is the default method routed to by the frontcontroller
     */
    public function actionIndex() {
        $this->actionRecentPosts();
    }

    /**
     * Lists all recent posts
     */
    public function actionRecentPosts() {
        $this->setView('blog/postlist');
        $this->pagetitle = 'Recent posts';
        $this->content->user = $this->_user;
        $this->content->heading = 'Most recent posts';
        $this->content->blogposts = BaseDAO::factory('Blogpost')->findByDate();
    }

    /**
     *  Lists blogs by number of views
     */
    public function actionTopBlogs() {
        $this->setView('blog/topbloglist');
        $this->pagetitle = 'Top blogs';
        $this->content->heading = 'Top blogs';
        // find IDs of topbloggers
        $userids = BaseDAO::factory('Blogpost')->findTopBloggerIds();
        $this->content->userids = $userids;
        // get the blogs of these IDs
        $this->content->users = BaseDAO::factory('User')->findManyById($userids);
    }

    /**
     * Lists all blogs
     */
    public function actionListAll() {
        // prepare the view
        $this->setView('blog/bloglist');
        $this->pagetitle = 'All blogs';
        $this->content->heading = 'All blogs';
        $this->content->users = BaseDAO::factory('User')->findAll();
    }

    /**
     *  Lists posts by number of views
     */
    public function actionPostsByViews() {
        // prepare the view
        $this->setView('blog/postlist');
        $this->pagetitle = 'Most viewed posts';
        $this->content->user = $this->_user;
        $this->content->heading = 'Most viewed posts';
        $this->content->blogposts = BaseDAO::factory('Blogpost')->findByViews();
    }

    /**
     * Lists posts by number of comments
     */
    public function actionPostsByComments() {
        // prepare the view
        $this->setView('blog/postlist');
        $this->pagetitle = 'Most commented posts';
        $this->content->heading = 'Most commended posts';
        $this->content->user = $this->_user;
        // find the most commented posts
        $postids = BaseDAO::factory('Comment')->findMostCommentedIds();
        // and get these posts
        $this->content->blogposts = BaseDAO::factory('Blogpost')->findManyById($postids);
        $this->content->blogposts = BaseDAO::factory('Blogpost')->findByComments();
    }

    /**
     * View a blog index
     * @param <type> $userid
     */
    public function actionView($userid) {
        $this->_check($userid);
        $this->setView('blog/viewblog');
        $this->content->user = $this->_user;
        $this->content->blogposts = BaseDAO::factory('Blogpost')->getBlog($userid);
        $this->sidebar->blogger = BaseDAO::factory('User')->findById($userid);
        // make sure we found something
        $this->_check($this->sidebar->blogger);
        // set the users preferred style
        $this->style = $this->sidebar->blogger->Template;
    }

    /**
     * View a single blogpost
     * @param <type> $postid
     */
    public function actionViewPost($postid) {
        $this->_check($postid);
        // prepare the view
        $this->setView('blog/viewpost');
        $this->content->user = $this->_user;

        // get the blogposts
        $dao = BaseDAO::factory('Blogpost');
        $this->content->blogpost = $dao->FindById($postid);
        $this->_check($this->content->blogpost);

        // get the comments and process the form to see if a comment has been posted
        $commentdao = BaseDAO::factory('Comment');
        $this->content->commentform = BaseModel::factory('comment');
        $this->_processCommentForm($commentdao, $postid, $this->content->commentform);
        $this->content->comments = $commentdao->findByPostId($postid);

        // assign some more needed things to the view
        $userid = $this->content->blogpost->UserId;
        $this->sidebar->blogger = BaseDAO::factory('User')->findById($userid);
        $this->style = $this->sidebar->blogger->Template;

        // increase the viewcount of the post
        $this->_updateViewCount($postid, $dao);
    }

    /**
     * Flag blogpost for review
     * @param <type> $postid
     */
    public function actionFlag($postid) {
        $this->_check($postid);
        // get the owner of the post
        $userid = current($this->_req->getParams(1));
        $this->_check($userid);
        BaseDAO::factory('Blogpost')->flag($postid);
        BaseDAO::factory('User')->updateFlaggedCount($userid);
        $this->_return($postid);
    }

    /**
     * Deletes a blogpost
     * @param <type> $postid
     */
    public function actionDelete($postid) {
        $this->_check($postid);
        // require user to be logged in as owner
        $userid = $this->requireOwner('Blogpost', $postid);
        BaseDAO::factory('Blogpost')->delete($postid);
        $this->_redirect('blog/view/' . $userid);
    }

    /**
     *  Updates the viewcount for a specific post
     * @param <type> $postid
     * @param <type> $dao
     * @return <type>
     */
    private function _updateViewCount($postid, $dao) {
        // if user has already been here
        if ($this->_user->hasVisited($postid))
            return;
        // else add the post to the session and update count
        $this->_user->addVisited($postid);
        $dao->updateViewCount($postid);
    }

    /**
     * Process and insert new comments
     * @param BaseDAO $dao
     * @param <type> $postid
     * @param <type> $comment
     * @return <type>
     */
    private function _processCommentForm(BaseDAO $dao, $postid, $comment) {
        // if no form is posted, return immediately
        if (!$this->_validForm($this->_req->getPost('form'), $comment)) {
            return;
        }
        $comment->BlogpostId = $postid;
        // if poster is logged on, add his userid to the comment
        if ($this->_user->isLogged()) {
            $comment->UserId = $this->_user->GetId();
        }
        // insert and redirect
        $comment->prepare();
        $dao->insert($comment);
        $this->_redirect('blog/viewpost/' . $postid);
    }

    public function actionTest() {
        BaseDAO::factory('Comment')->countByPostId();
    }

}