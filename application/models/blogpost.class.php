<?php

/**
 * @author     Lars Kristian Dahl <http://www.krisd.com>
 * @copyright  Copyright (c) 2011 Lars Kristian Dahl <http://www.krisd.com>
 * @license    http://www.apache.org/licenses/LICENSE-2.0
 * @version    SVN: $Id$
 */

namespace ebbs\models;

use ebbs\libraries\Validator;

/**
 * Description of Blogpost
 *
 * @author  Lars Kristian Dahl <http://www.krisd.com>
 */
class BlogPost extends \ebbs\models\BaseModel {

    /**
     * Defines valid attributes
     * @return <type>
     */
    public function validAttribs() {
        return array(
            'BlogpostId',
            'UserId',
            'Title',
            'Text',
            'Created',
            'Modified',
            'ViewCount',
            'Hidden',
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
        'Title' => array('string' => array('min' => 4, 'max' => 255), 'required'),
        'Text' => array('string', 'required'),

        );
    }

    /**
     * Validates the model and returns true if valid, otherwise false
     * @return <type>
     */
    public function validate() {
        Validator::factory($this)->validate();
        return!$this->getErrors();
    }

    /**
     *  Prepares the model for persistence
     */
    public function prepare() {
        if (!isset($this->Created))
            $this->Created = date('YmdHis', time());
        if (isset($this->Modified))
            unset($this->Modified);
    }

    /**
     * Populates the model with a default welcome post and returns itself
     * @param <type> $userid
     * @return BlogPost
     */
    public function welcomePost($userid) {
        $this->UserId = $userid;
        $this->Title = 'Congratulations on your new blog!';
        $this->Text = '<p>Welcome to your new blog. This is simply an example post, feel free to delete it and add your own!
            You can do so by using the menu in the upper right corner.
            From there you can also manage your profile and other things related to your account.</p>';
        $this->prepare();
        return $this;
    }

}