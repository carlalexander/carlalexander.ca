<?php

/*
Plugin Name: Helthe Error Monitoring
Plugin URI: http://www.helthe.co
Description: World class error monitoring for WordPress.
Version: Beta
Author: Carl Alexander
Author URI: http://www.carlalexander.ca
License: GPL3
*/

// Setup class autoloader
require_once __DIR__ . '/Helthe/Autoloader.php';
Helthe_Autoloader::register();

// Register plugin
Helthe_Plugin::register();
