<?php

/**
 * @author     Lars Kristian Dahl <http://www.krisd.com>
 * @copyright  Copyright (c) 2011 Lars Kristian Dahl <http://www.krisd.com>
 * @license    http://www.apache.org/licenses/LICENSE-2.0
 * @version    SVN: $Id$
 */

namespace ebbs\models;

use \ebbs\libraries\WebUser;
use \ebbs\libraries as lib;

/**
 * Model for handling logins
 *
 * @author  Lars Kristian Dahl <http://www.krisd.com>
 */
class LoginForm extends BaseModel {

    private $_rememberMe = false;

    /**
     * Defines the valid attributes
     * @return <type>
     */
    public function validAttribs() {
        return array(
            'username',
            'password',
            'remember_me',
        );
    }

    /**
     * Defines the validation rules
     * Not defining any to provide as little information as possible about login procedures
     */
    public function rules() {

    }

    /**
     * Validates the model, and performs authentication
     * @return <type>
     */
    public function validate() {
        return $this->_authenticate(WebUser::factory());
    }

    /**
     * Prepares the model for persistence
     */
    public function prepare() {

    }

    /**
     * Performs authentication based on provided username and password
     * @param WebUser $webuser
     * @return <type>
     */
    private function _authenticate(WebUser $webuser) {
        $user = $webuser->authenticate($this->username, $this->password);
        if (!$user) {
            $this->addError('password', 'Incorrect username or password');
            return false;
        } else
            return $user;
    }

}