<?php

/**
 * @author     Lars Kristian Dahl <http://www.krisd.com>
 * @copyright  Copyright (c) 2011 Lars Kristian Dahl <http://www.krisd.com>
 * @license    http://www.apache.org/licenses/LICENSE-2.0
 * @version    SVN: $Id$
 */

namespace ebbs\controllers\user;

use ebbs\libraries as lib;
use ebbs\models\dao\BaseDAO;
use ebbs\models\BaseModel;
use ebbs\views\View;

/**
 * ActionController for the ebbs User module.
 *
 * @author  Lars Kristian Dahl <http://www.krisd.com>
 */
class UserController extends \ebbs\controllers\ActionController {

    protected $_pagetitle = 'Users';

    /**
     * Called before every dispatch to this controller
     */
    public function preDispatch() {

    }

    /**
     * Called after every dispatch to the controller
     */
    public function postDispatch() {

    }

    /**
     * This is the default method routed to by the frontcontroller
     */
    public function actionIndex() {
        $this->actionList();
    }

    /**
     * Register a new user
     */
    public function actionRegister() {
        $user = BaseModel::factory('user');
        // validate form and model
        if ($this->_validForm($this->_req->getPost('form'), $user)) {
            // prepare for persistence and insert
            $user->prepare();
            $user = BaseDAO::factory('User')->insert($user);
            BaseDAO::factory('Blogpost')->insert(BaseModel::factory('Blogpost')->welcomePost($user->UserId));
            // log the user in and redirect
            $this->_user->forceLogin($user);
            $this->_redirect('blog/view/' . $user->UserId);
        }
        // prepare the views
        $this->setView('user/register');
        $this->pagetitle = 'Register';
        $this->content->user = $user;
    }

    /**
     * User login
     */
    public function actionLogin() {
        if ($this->_user->isLogged()) {
            $this->_redirect();
        }
        // TODO clean up this action
        $loginform = BaseModel::factory('loginform');
        $formdata = $this->_req->getPost('form');
        // if form has been posted
        if ($formdata) {
            $loginform->setData($formdata);
            // validate/authenticate and take action accordingly
            $user = $loginform->validate();
            if ($user) {
                if ($user->Admin) {
                    $this->_redirect('admin');
                } else {
                    $this->_redirect('blog/view/' . $user->UserId);
                }
            }
        }
        // prepare the view
        $this->setView('user/login');
        $this->pagetitle = "Log in";
        $this->content->form = $loginform;
    }

    /**
     * Show a users profile
     * @param <type> $userid
     */
    public function actionProfile($userid) {
//    TODO Add possibility to view by username
        $this->_check($userid);
        $dao = BaseDAO::factory('User');
        $user = $dao->findById($userid);
        $this->_check($user);
        // prepare the view
        $this->setView('user/profile');
        $this->pagetitle = 'User Profile';
        $this->content->user = $user;
    }

    /**
     * Performs a search by username or blogtitle
     */
    public function actionSearch() {
        $form = $this->_req->getGet('form');
        $search = $form['search'];
        $this->setView('blog/bloglist');
        $this->pagetitle = "Search";
        $this->content->heading = 'Search results for "' . $search . '" :';
        $this->content->users = BaseDAO::factory('User')->findBySearch($search);
    }

    public function actionResetPassword() {
        $form = $this->_req->getGet('form');
        $email = $form['email'];
        $this->setView('user/resetpassword');
        $this->pagetitle = "Reset Password";
        $this->content->resetform = BaseModel::factory('resetpassword');

    }

}