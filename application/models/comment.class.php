<?php

/**
 * @author     Lars Kristian Dahl <http://www.krisd.com>
 * @copyright  Copyright (c) 2011 Lars Kristian Dahl <http://www.krisd.com>
 * @license    http://www.apache.org/licenses/LICENSE-2.0
 * @version    SVN: $Id$
 */

namespace ebbs\models;

use ebbs\libraries\Session;
use ebbs\libraries\Validator;

/**
 * Description of Comment
 *
 * @author  Lars Kristian Dahl <http://www.krisd.com>
 */
class Comment extends \ebbs\models\BaseModel {

    /**
     * Defines valid attributes
     * @return <type>
     */
    public function validAttribs() {
        return array(
            'CommentId',
            'BlogpostId',
            'UserId',
            'Author',
            'Email',
            'WebUrl',
            'Text',
            'Created',
            'Modified',
            'Flagged',
            'Deleted'
        );
    }

    /**
     * Defines the validation rules
     * @return <type>
     */
    public function rules() {
        return array(
            'Author' => array('string' => array('min' => 2, 'max' => 20), 'required'),
            'Email' => array('email' => array('required' => 0)),
            'WebUrl' => array('url' => array('required' => 0)),
            'Text' => array('string' => array('min' => 8), 'required', 'captcha')
        );
    }

    /**
     * Magic tostring method
     * @return <type>
     */
    public function __toString() {
        return $this->_data->Text;
    }

    /**
     * Validates the model
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
    }

}