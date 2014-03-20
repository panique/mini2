<?php

/**
 * A simple PHP MVC skeleton
 *
 * @package php-mvc
 * @author Panique
 * @link http://www.php-mvc.net
 * @link https://github.com/panique/php-mvc/
 * @license http://opensource.org/licenses/MIT MIT License
 */

// load the (optional) Composer auto-loader
if (file_exists('vendor/autoload.php')) {
    require 'vendor/autoload.php';
}

// load application config (error reporting etc.)
require 'application/config/config.php';

// load application class
require 'application/libs/application.php';
require 'application/libs/controller.php';

// run the scss compiler every you the application is hit (in development)
// TODO: build a switch for development/production
SassCompiler::run("public/scss/", "public/css/");

// call error method from additional class
// this make pretty error reporting in your application 
Additional::error();

// call log method from additional class
// build user access log for yout application 
Additional::log();

// start the application
$app = new Application();
