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
 * Helthe deprecated logger. Trigger E_USER_DEPRECATED errors when deprecated operations are performed.
 *
 * @author Carl Alexander
 */
class Helthe_Logger_DeprecatedLogger
{
    /**
     * Register the logger with the appropriate WordPress hooks.
     */
    public static function register()
    {
        $logger = new self();

        add_action('deprecated_function_run', array($logger, 'logDeprecatedFunction'), 10, 3);
        add_action('deprecated_file_included', array($logger, 'logDeprecatedFile'), 10, 4);
        add_action('deprecated_argument_run', array($logger, 'logDeprecatedArgument'), 10, 4);
    }

    /**
     * Triggered when a deprecated function is run.
     *
     * @param string $function
     * @param string $replacement
     * @param string $version
     */
    public function logDeprecatedFunction($function, $replacement, $version)
    {
        if (null !== $replacement) {
            $errorMessage = sprintf('%1$s is deprecated since version %2$s! Use %3$s instead.', $function, $version, $replacement);
        } else {
            $errorMessage = sprintf('%1$s is deprecated since version %2$s with no alternative available.', $function, $version);
        }

        $this->triggerError($errorMessage);
    }

    /**
     * Triggered when a deprecated function is included.
     *
     * @param string $file
     * @param string $replacement
     * @param string $version
     * @param string $message
     */
    public function logDeprecatedFile($file, $replacement, $version, $message)
    {
        if (null !== $replacement) {
            $errorMessage = sprintf('%1$s is deprecated since version %2$s! Use %3$s instead.', $file, $version, $replacement) . $message;
        } else {
            $errorMessage = sprintf('%1$s is deprecated since version %2$s with no alternative available.', $file, $version) . $message;
        }

        $this->triggerError($errorMessage);
    }

    /**
     * Triggered when a deprecated argument is used.
     *
     * @param string $function
     * @param string $message
     * @param string $version
     */
    public function logDeprecatedArgument($function, $message, $version)
    {
        if (null !== $message) {
            $errorMessage = sprintf('%1$s was called with an argument that is deprecated since version %2$s! %3$s', $function, $version, $message);
        } else {
            $errorMessage = sprintf('%1$s was called with an argument that is deprecated since version %2$s with no alternative available.', $function, $version);
        }

        $this->triggerError($errorMessage);
    }

    /**
     * Trigger a PHP error.
     *
     * @param string $message
     */
    public function triggerError($message)
    {
        $type = E_USER_NOTICE;

        // Cannot use E_USER_DEPRECATED till PHP 5.3.
        if (version_compare(PHP_VERSION, '5.3.0', '>=')) {
            $type = E_USER_DEPRECATED;
        }

        do_action('helthe_trigger_deprecated', $message);

        trigger_error($message, $type);
    }
}
