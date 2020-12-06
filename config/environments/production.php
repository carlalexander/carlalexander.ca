<?php

/**
 * Configuration overrides for WP_ENV === 'production'
 */

use Roots\WPConfig\Config;

Config::define('WP_SENTRY_PHP_DSN', env('WP_SENTRY_PHP_DSN'));
