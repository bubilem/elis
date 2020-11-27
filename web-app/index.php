<?php
error_reporting(E_ALL);

/* UTF-8 string encoding */
mb_internal_encoding("UTF-8");

/* Start SESSIONS */
session_start();

/* Class Exception  include */
require_once('app/elis/controller/Exception.php');

/* Register the function for the exception handler */
set_exception_handler("elis\controller\Exception::exceptionHandler");

/* Class loader  include */
require_once('app/elis/controller/Loader.php');

/* Regiter the function for autoloading the classes */
spl_autoload_register('elis\controller\Loader::loadClass');

/* Configuration file loading */
elis\utils\Conf::load('conf.ini');

/* Router starts the application */
elis\controller\Router::route(filter_input(INPUT_SERVER, 'REQUEST_URI'));
