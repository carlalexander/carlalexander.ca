<?php

/**
 * Configuration overrides for WP_ENV === 'development'
 */

use Roots\WPConfig\Config;

Config::define('SAVEQUERIES', true);
Config::define('WP_DEBUG', true);
Config::define('WP_DEBUG_DISPLAY', true);
Config::define('WP_DISABLE_FATAL_ERROR_HANDLER', true);
Config::define('SCRIPT_DEBUG', true);

ini_set('display_errors', 1);

// Enable plugin and theme updates and installation from the admin
Config::define('DISALLOW_FILE_MODS', false);

Config::define('WPMS_ON', true);
Config::define('WPMS_MAILER', 'smtp');
Config::define('WPMS_SET_RETURN_PATH', 'false');
Config::define('WPMS_SSL', '');
Config::define('WPMS_SMTP_AUTH', false);
Config::define('WPMS_SMTP_HOST', 'localhost');
Config::define('WPMS_SMTP_PORT', 1025);
