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
 * Manages the admin pages for the plugin.
 *
 * @author Carl Alexander
 */
class Helthe_Admin_Page
{
    /**
     * @var array
     */
    private $options;

    /**
     * Register the admin page class with all the appropriate WordPress hooks.
     *
     * @param array $options
     */
    public static function register(array $options = array())
    {
        $page = new self($options);

        add_action('admin_init', array($page, 'configure'));
        add_action('admin_menu', array($page, 'addAdminPage'));
    }

    /**
     * Constructor.
     *
     * @param array $options
     */
    public function __construct(array $options = array())
    {
        $this->options = $options;
    }

    /**
     * Adds the admin page.
     */
    public function addAdminPage()
    {
        add_options_page(__('Helthe Error Monitoring', 'helthe'), __('Helthe Monitoring', 'helthe'), 'install_plugins', 'helthe', array($this, 'render'));
    }

    /**
     * Use the Settings API to configure the option page.
     */
    public function configure()
    {
        // Register settings
        register_setting('helthe', 'helthe');

        // General Section
        add_settings_section('helthe-general', __('General', 'helthe'), array($this, 'renderGeneralSection'), 'helthe');
        add_settings_field('helthe-error-reporting', __('Error Reporting Level', 'helthe'), array($this, 'renderErrorReportingField'), 'helthe', 'helthe-general');

    }

    /**
     * Renders the admin page using the Settings API.
     */
    public function render()
    {
        ?>
        <div class="wrap" id="helthe-admin">
            <div id="icon-tools" class="icon32"><br></div>
            <h2><?php _e('Helthe Error Monitoring Configuration', 'helthe'); ?></h2>
            <form action="options.php" method="POST">
                <?php settings_fields('helthe'); ?>
                <?php do_settings_sections('helthe'); ?>
                <?php submit_button(); ?>
            </form>
        </div>
        <?php
    }

    /**
     * Renders the general section.
     */
    public function renderGeneralSection()
    {
        ?>
        <p><?php _e('These settings help WordPress log errors.', 'helthe'); ?></p>
        <?php
    }

    /**
     * Renders the error reporting field.
     */
    public function renderErrorReportingField()
    {
        $option = isset($this->options['error_reporting']) ? $this->options['error_reporting'] : null;
        ?>
        <select id="helthe_error_reporting" name="helthe[error_reporting]">
            <option><?php _e('WordPress Default', 'helthe'); ?></option>
            <option value="prod" <?php selected($option, 'prod'); ?>><?php _e('Production Server', 'helthe'); ?></option>
            <option value="all" <?php selected($option, 'all'); ?>><?php _e('All Errors', 'helthe'); ?></option>
            <option value="none" <?php selected($option, 'none'); ?>><?php _e('None', 'helthe'); ?></option>
        </select>
        <p class="description"><?php _e('This allows you to configure what errors get logged by PHP.', 'helthe'); ?></p>
        <?php
    }
}
