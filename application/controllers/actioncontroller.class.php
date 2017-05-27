<?php

/**
 * @author     Lars Kristian Dahl <http://www.krisd.com>
 * @copyright  Copyright (c) 2011 Lars Kristian Dahl <http://www.krisd.com>
 * @license    http://www.apache.org/licenses/LICENSE-2.0
 * @version    SVN: $Id$
 */

namespace ebbs\controllers;

use \ebbs\views\View;
use \ebbs\libraries\Request;
use \ebbs\libraries\WebUser;
use \ebbs\libraries\Config;
use ebbs\libraries\AuthException;

/**
 * Base for all ActionControllers
 *
 * @author  Lars Kristian Dahl <http://www.krisd.com>
 */
abstract class ActionController extends BaseController {


    protected $_pagetitle;

    /**
     * These variables hold the different views
     * @var <type>
     */
    protected $_layout;
    protected $_topmenu;
    protected $_sidebar;
    protected $_content = '';

    /**
     * Holds the current web user
     * @var <type>
     */
    protected $_user;

    /**
     * Factory method, enables dependency injection
     * @param <type> $controller
     * @param Request $request
     * @return controller
     */
    public static function factory($controller, Request $request) {
        return new $controller($request, WebUser::factory());
    }

    /**
     * Constructor
     * Sets up the actioncontrollers for general use, concrete classes should
     * call this constructor if overriden
     * @param Request $request
     * @param WebUser $user
     */

    public function __construct(Request $request, WebUser $user) {
        parent::__construct($request);
        $this->_user = $user;

        // Sets some default values, these can be overriden
        $this->_pagetitle = Config::get('default_pagetitle');
        $this->_layout = Config::get('default_layout');
        $this->_topmenu = 'common/topmenu';
        $this->_sidebar = Config::get('default_sidebar');

        // Sets up the layout view with the default values
        $this->_layout = View::factory($this->_layout);
        $this->_req->setResponse($this->_layout);
        $this->pagetitle = $this->_pagetitle;
        $this->content = $this->_content;

        // Sets up the topmenu with a reference to the user
        $this->topmenu = View::factory($this->_topmenu);
        $this->topmenu->user = $user;

        // Sets up the sidebar with a reference to the user and the request
        $this->sidebar = View::factory($this->_sidebar);
        $this->sidebar->user = $user;
        $this->sidebar->request = $request;
    }

    /**
     * Magic getter for properties in the layout view
     * @param <type> $name
     * @return <type>
     */
    public function __get($name) {
        return (isset($this->_layout->$name)) ? $this->_layout->$name : null;
    }

    /**
     * Magic setter for properties in the layout view
     * @param <type> $name
     * @param <type> $value
     */
    public function __set($name, $value) {
        $this->_layout->$name = $value;
    }

    /**
     * This is called before every dispatch to a controller
     */
    abstract public function preDispatch();

    /**
     * This is called after every dispatch to a controller
     */
    abstract public function postDispatch();

    /**
     * This is the default method routed to by the frontcontroller
     */
    abstract public function actionIndex();

    /**
     * Sets the content view
     * @param <type> $view
     */
    protected function setView($view) {
        $this->content = View::factory($view);
    }

    /**
     * Validates a filled form in relation to the model specified
     * @param <type> $form
     * @param <type> $model
     * @return <type>
     */
    protected function _validForm($form, $model) {
        return ($form != null && $model->SetData($form)->validate()) ? true : false;
    }

    /**
     * Checks if a user is logged in, and throws an exception if not
     * @return <type>
     */
    protected function requireLogin() {
        if ($this->_user->isLogged()) {
            return $this->_user->getId();
        } else {
            throw new AuthException('You need to be logged in to perform this operation.');
        }
    }

    /**
     * Checks if a user is the owner of the specified entity, in a given table,
     * and throws an exception if not.
     * @param <type> $table
     * @param <type> $postid
     * @return <type>
     */
    protected function requireOwner($table, $id) {
        if ($this->_user->isOwner($table, $id)) {
            return $this->_user->getId();
        } else {
            throw new AuthException('You need to be logged in as the author to perform this operation.');
        }
    }

    /**
     * Checks if a user is an administrator, throws an exception if not
     * @return <type>
     */
    protected function requireAdmin() {
        if ($this->_user->isAdmin()) {
            return $this->_user->getId();
        } else {
            throw new AuthException('You need to be logged in as an Administrator to perform this operation.');
        }
    }

    /**
     * Checks if an expression evaluates to true, throws a error if not
     * @param <type> $id
     */
    protected function _check($expression) {
        // TODO add HttpErrorException class
        if (!$expression) {
            header("HTTP/1.1 404 Not Found");
            exit(1);
        }
    }

}