<?php

/*
 * @author     Lars Kristian Dahl <http://www.krisd.com>
 * @version    $Id$
 * @copyright  Copyright (c) 2011 Lars Kristian Dahl <http://www.krisd.com>
 * @license    http://www.apache.org/licenses/LICENSE-2.0
 */

namespace ebbs;

use ebbs\libraries\Request;
use ebbs\controllers\FrontController;
use ebbs\libraries\AuthException;
use ebbs\libraries\Config;

session_start();

// for production
//ini_set("display_errors", "Off");
//error_reporting(E_ALL);

$site_path = realpath(dirname(__FILE__));

// define the site path
define('__BASE_PATH', $site_path);

// set the application path
define('__APP_PATH', __BASE_PATH . '/application');

// set the library path
define('__LIB_PATH', __APP_PATH . '/libraries');

// set the public web root path
define('__BASE_URL', 'http://' . $_SERVER['SERVER_NAME']);

include __BASE_PATH . '/bootstrapper.php';

/**
 * DEBUG superglobal vardumps
 */
//var_dump($_REQUEST);
//var_dump($_GET);
//var_dump($_POST);
//var_dump($_COOKIE);
//var_dump($_SESSION);
//var_dump($_SERVER);
//var_dump($_FILES);
// instantiate FrontController and perform routing
try {
    FrontController::factory()->route();

    // catch any exceptions
} catch (AuthException $e) {
    $e->redirect(Config::get('loginpage'));
} catch (\ErrorException $e) {
    trigger_error($e->getMessage(), E_USER_ERROR);
//    die('Fatal exception: ' . $e->getMessage());
} catch (\Exception $e) {
    trigger_error($e->getMessage(), E_USER_ERROR);
//    die('Exception caught: ' . $e->getMessage());
//    var_dump($e);
}