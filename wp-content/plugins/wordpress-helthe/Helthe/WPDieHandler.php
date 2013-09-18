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
 * Helthe handler for wp_die.
 *
 * @author Carl Alexander
 */
class Helthe_WPDieHandler
{
    /**
     * Old AJAX handler.
     *
     * @var mixed
     */
    private $oldAjaxHandler;
    /**
     * Old Default handler.
     *
     * @var mixed
     */
    private $oldDefaultHandler;
    /**
     * Old XMLRPC handler.
     *
     * @var mixed
     */
    private $oldXmlHandler;

    /**
     * Register the handler with the appropriate WordPress filters.
     */
    public static function register()
    {
        $handler = new self();

        // Register the handlers as late as possible to allow other plugins to use the hooks.
        add_filter('wp_die_ajax_handler', array($handler, 'registerAjaxHandler'), 9999);
        add_filter('wp_die_handler', array($handler, 'registerDefaultHandler'), 9999);
        add_filter('wp_die_xmlrpc_handler', array($handler, 'registerXmlHandler'), 9999);
    }

    /**
     * Filter that registers our AJAX handler and saves the current one.
     *
     * @param mixed $handler
     *
     * @return array
     */
    public function registerAjaxHandler($handler)
    {
        $this->oldAjaxHandler = $handler;

        return array($this, 'handleAjax');
    }

    /**
     * Filter that registers our default handler and saves the current one.
     *
     * @param mixed $handler
     *
     * @return array
     */
    public function registerDefaultHandler($handler)
    {
        $this->oldDefaultHandler = $handler;

        return array($this, 'handleDefault');
    }

    /**
     * Filter that registers our XMLRPC handler and saves the current one.
     *
     * @param mixed $handler
     *
     * @return array
     */
    public function registerXmlHandler($handler)
    {
        $this->oldXmlHandler = $handler;

        return array($this, 'handleXml');
    }

    /**
     * Handles when the WordPress execution is killed during an AJAX request.
     *
     * @param string $message
     */
    public function handleAjax($message = '')
    {
        // Only trigger an error there is an actual message.
        if (!empty($message) && !is_numeric($message) && !self::isJson($message)) {
            do_action('helthe_wp_die_ajax_error', sprintf(__('An AJAX request returned the following error: %1$s', 'helthe'), $message));
        }

        // Call the registered handler
        if (is_callable($this->oldAjaxHandler)) {
            call_user_func($this->oldAjaxHandler, $message);
        }
    }

    /**
     * Default handler for when WordPress execution is killed.
     *
     * @param mixed  $message
     * @param string $title
     * @param array  $args
     */
    public function handleDefault($message = '', $title = '', $args = array())
    {
        if ($message instanceof WP_Error) {
            do_action('helthe_wp_die_default_error', sprintf(__('A request returned the following error: %1$s', 'helthe'), $message->get_error_message()));
        }

        // Call the registered handler
        if (is_callable($this->oldDefaultHandler)) {
            call_user_func($this->oldDefaultHandler, $message, $title, $args);
        }
    }

    /**
     * Handles when the WordPress execution is killed during a XMLRPC request.
     *
     * @param mixed  $message
     * @param string $title
     * @param array  $args
     */
    public function handleXml($message = '', $title = '', $args = array())
    {
        // Only trigger an error there is an actual message.
        if (!empty($message)) {
            do_action('helthe_wp_die_xml_error', sprintf(__('A XMLRPC request returned the following error: %1$s', 'helthe'), $message));
        }

        // Call the registered handler
        if (is_callable($this->oldXmlHandler)) {
            call_user_func($this->oldXmlHandler, $message, $title, $args);
        }
    }

    /**
     * Checks if the string is formated in JSON.
     *
     * @param string $string
     *
     * @return boolean
     */
    private static function isJson($string)
    {
        return is_array(json_decode($string, true));
    }
}
