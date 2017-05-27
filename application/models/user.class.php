<?php

/*
 * @author     Lars Kristian Dahl <http://www.krisd.com>
 * @copyright  Copyright (c) 2011 Lars Kristian Dahl <http://www.krisd.com>
 * @license    http://www.apache.org/licenses/LICENSE-2.0
 * @version    SVN: $Id$
 */

namespace ebbs\models;

use ebbs\libraries\Validator;
use ebbs\libraries\Config;
use ebbs\libraries\WebUser;

/**
 * Class for managing users
 *
 * @author Lars Kristian Dahl <http://www.krisd.com>
 */
class User extends \ebbs\models\BaseModel {

    /**
     * Defines valid model attributes
     * @return <type>
     */
    public function validAttribs() {
        return array(
            'UserId',
            'Username',
            'Password',
            'ConfirmPassword',
            'FirstName',
            'LastName',
            'Email',
            'WebUrl',
            'About',
            'CountryId',
            'BlogTitle',
            'Template',
            'ProfileImage',
            'LoginCount',
            'LastLogin',
            'Created',
            'Modified',
            'Blocked',
            'Admin',
            'FlaggedCount'
        );
    }

    /**
     * Defines the validation rules
     * @return <type>
     */
    public function rules() {
        return array(
            'Username' => array('string' => array('min' => 4, 'max' => 15), 'required'),
            'Password' => array('match' => array('ConfirmPassword'), 'string' => array('min' => 8, 'max' => 20), 'required'),
            'FirstName' => array('string', 'required'),
            'LastName' => array('string', 'required'),
            'Email' => array('email'),
//            'WebUrl' => array('url' => array('required' => 0)),
            'BlogTitle' => array('string' => array('min' => 5, 'max' => 127), 'required'),
            'Template' => array('required'),
            'ProfileImage' => array('file' => array('type' => array('image/jpeg', 'image/pjpeg', 'image/png', 'image/gif'), 'size' => 200000))
        );
    }

    /**
     * Performs model validation
     * @return <type>
     */
    public function validate() {
        Validator::factory($this)->validate();
        return!$this->getErrors();
    }

    /**
     * Prepares the model for persistence
     */
    public function prepare() {
        if (!isset($this->Created))
            $this->Created = date('YmdHis', time());

        if (isset($this->ConfirmPassword))
            unset($this->ConfirmPassword);

        $this->Password = WebUser::getHash($this->Password);
        $this->ProfileImage = $this->_processProfileImage();
    }

    /**
     * Processes the profile image, returning the filename
     * @return string
     */
    public function _processProfileImage() {
        if ($_FILES['form']['size']['ProfileImage']) {
            $pathinfo = pathinfo($_FILES['form']['name']['ProfileImage']);
            $file = uniqid() . '.' . $pathinfo['extension'];
            $target = __BASE_PATH . '/' . Config::get('profileimg_path') . $file;
            if (!\move_uploaded_file($_FILES['form']['tmp_name']['ProfileImage'], $target)) {
                throw new \Exception('There was an error uploading the file.');
            }
            return $file;
        } else {
            return 'user_profile.gif';
        }
    }

}