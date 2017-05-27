<?php

/*
 * @author     Lars Kristian Dahl <http://www.krisd.com>
 * @copyright  Copyright (c) 2011 Lars Kristian Dahl <http://www.krisd.com>.
 * @license    http://www.apache.org/licenses/LICENSE-2.0
 * @version    SVN: $Id$
 */

namespace ebbs\libraries;

/**
 * VERY simple configuration class
 *
 * @author  Lars Kristian Dahl <http://www.krisd.com>
 */
class Config {

    private static $_config = array(
        'dbinfo' => array(
//          DB CONFIG
            'type' => 'mysql',
            'host' => '127.0.0.1',
            'port' => '3306',
            'dbname' => 'ez_ebbs',
            'username' => 'root',
            'password' => ''
        ),
//       MISC CONFIG
        'dfeault_pagetitle' => 'EBBS',
        'loginpage' => 'user/login',
        'default_layout' => 'common/indexlayout',
        'default_sidebar' => 'common/sidebar',
        'default_controller' => 'blog',
        'default_action' => 'index',
        'upload_path' => 'media/uploads/',
        'profileimg_path' => 'media/uploads/profileimg/',
        'session_lifetime' => 0,
        'styles' => array('default', 'sunset'),
    );

    // disallows instantiation
    private function __construct() {

    }

    // Returns a configuration value
    public static function get($key) {
        return (isset(self::$_config[$key])) ? self::$_config[$key] : null;
    }

    // Sets a configuration value
    public static function set($key, $value) {
        self::$_config[$key] = $value;
    }

}