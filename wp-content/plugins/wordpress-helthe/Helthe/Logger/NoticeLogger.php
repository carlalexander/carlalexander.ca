<?php

/*
 * This file is part of the WordPress Helthe plugin.
 *
 * (c) Helthe
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * Helthe notice logger. Triggers E_USER_NOTICE errors.
 *
 * @author Carl Alexander
 */
class Helthe_Logger_NoticeLogger
{
    /**
     * Register the logger with the appropriate WordPress hooks.
     */
    public static function register()
    {
        $logger = new self();

        add_action('doing_it_wrong_run', array($logger, 'logWrong'), 10, 2);
        add_action('http_api_debug', array($logger, 'logHttp'), 10, 5);
    }

    /**
     * Triggered when a function is improperly called.
     *
     * @param string $function
     * @param string $message
     */
    public function logWrong($function, $message)
    {
        $this->triggerError(sprintf('%1$s was called incorrectly. %2$s', $function, $message));
    }

    /**
     * Checks http responses for errors.
     *
     * @param mixed  $response
     * @param string $type
     * @param string $class
     * @param array  $args
     * @param string $url
     */
    public function logHttp($response, $type, $class, $args, $url)
    {
        if (!$response instanceof WP_Error) {
            return;
        }

        $this->triggerError(sprintf('%1$s returned "%2$s" when trying to reach "%3$s".', $class, $response->errors['http_request_failed'][0], $url));
    }

    /**
     * Trigger a PHP error.
     *
     * @param string $message
     */
    public function triggerError($message)
    {
        do_action('helthe_trigger_notice', $message);

        trigger_error($message, E_USER_NOTICE);
    }
}
