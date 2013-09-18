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
 * WP_Filesystem implementation used by Helthe for error monitoring.
 * This class is a proxy around the filesystem implementation chosen by WordPress.
 *
 * @author Carl Alexander
 */
class WP_Filesystem_Helthe
{
    /**
     * @var string
     */
    private static $method = false;
    /**
     * @var WP_Filesystem_Base
     */
    private $filesystem;

    /**
     * Register the filesystem with the appropriate WordPress filters.
     */
    public static function register()
    {
        // Hook in as late as possible to allow other plugins to leverage the filter.
        add_filter('filesystem_method', array('WP_Filesystem_Helthe', 'registerMethod'), 9999, 2);
    }

    /**
     * Registers Helthe as a filesystem method. Saves the current method.
     *
     * @param string $method
     * @param array  $args
     *
     * @return string
     */
    public static function registerMethod($method, $args)
    {
        if (false === $method) {
            return $method;
        }

        self::$method = $method;

        // Check empty args for request_filesystem_credentials function.
        if (empty($args)) {
            return $method;
        }

        return 'Helthe';
    }

    /**
     * Constructor.
     *
     * @param mixed $options
     */
    public function __construct($options = '')
    {
        $class = 'WP_Filesystem_' . self::$method;

        if (!class_exists($class)) {
            $this->loadClassFile();
        }

        $this->filesystem = new $class($options);
    }

    /**
     * Send all method calls to filesystem object.
     *
     * @param string $method
     * @param array  $args
     *
     * @return mixed
     */
    public function __call($method, $args)
    {
        $result = call_user_func_array(array($this->filesystem, $method), $args);

        return $result;
    }

    /**
     * Send all calls to variables to the filesystem object.
     *
     * @param string $name
     *
     * @return mixed
     */
    public function __get($name)
    {
        return $this->filesystem->$name;
    }

    /**
     * Loads the class file.
     */
    private function loadClassFile()
    {
        $file = apply_filters('filesystem_method_file', ABSPATH . 'wp-admin/includes/class-wp-filesystem-' . self::$method . '.php', self::$method);

        if (!file_exists($file)) {
            do_action('helthe_filesystem_not_found', sprintf(__('Could not find the file "%1$s" for the %2$s file system method.', 'helthe'), $file, self::$method));
        }

        require_once($file);
    }
}
