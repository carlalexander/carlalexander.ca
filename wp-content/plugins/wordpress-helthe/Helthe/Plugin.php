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
 * Helthe Plugin.
 *
 * @author Carl Alexander
 */
class Helthe_Plugin
{
    /**
     * @var array
     */
    private $options;

    /**
     * Registers the plugin and and its dependencies.
     */
    public static function register()
    {
        $plugin = new self();

        // Handlers
        Helthe_ErrorHandler::register($plugin->getErrorReportingLevel());
        Helthe_WPDieHandler::register();

        // Replace WordPress objects with proxies
        Helthe_Proxy_WPDB::initialize();
        Helthe_Proxy_ImageEditor::register();
        WP_Filesystem_Helthe::register();

        // Admin
        Helthe_Admin_Bar::register();
        Helthe_Admin_Page::register($plugin->getOptions());

        // Loggers
        Helthe_Logger_WarningLogger::register();
        Helthe_Logger_NoticeLogger::register();
        Helthe_Logger_DeprecatedLogger::register();

        // Hooks
        add_action('activated_plugin', array($plugin, 'ensureLoadedFirst'));
    }

    /**
     * Constructor.
     */
    public function __construct()
    {
        $this->options = get_option('helthe', array());
    }

    /**
     * Ensures that the plugin is always the first one to be loaded. This is critical otherwise
     * the error monitoring might not capture all the errors.
     */
    public function ensureLoadedFirst()
    {
        $plugin = 'wordpress-helthe/wp-helthe.php';
        $plugins = get_option('active_plugins');
        $key = array_search($plugin, $plugins);

        if ($key) {
            array_splice($plugins, $key, 1);
            array_unshift($plugins, $plugin);
            update_option('active_plugins', $plugins);
        }
    }

    /**
     * Get the plugin options.
     *
     * @return array
     */
    public function getOptions()
    {
        return $this->options;
    }

    /**
     * Get the selected error reporting level bit mask.
     *
     * @return integer|null
     */
    public function getErrorReportingLevel()
    {
        if (!isset($this->options['error_reporting'])) {
            return null;
        }

        switch ($this->options['error_reporting']) {
            case 'prod':
                return E_ALL & ~E_STRICT;
            case 'all':
                return E_ALL | E_STRICT;
            case 'none':
                return 0;
            default:
                return null;
        }
    }
}
