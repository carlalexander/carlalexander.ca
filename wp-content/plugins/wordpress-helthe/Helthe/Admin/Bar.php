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
 * Manages the admin bar for the plugin.
 *
 * @author Carl Alexander
 */
class Helthe_Admin_Bar
{
    /**
     * All the errors collected.
     *
     * @var array
     */
    private $errors = array();

    /**
     * Register the admin bar class with all the appropriate WordPress hooks.
     */
    public static function register()
    {
        $bar = new self();

        add_action('admin_bar_menu', array($bar, 'generate'));
        add_action('helthe_handle_error', array($bar, 'collectError'));
    }

    /**
     * Generate the admin bar menu.
     *
     * @param WP_Admin_Bar $bar
     */
    public function generate(WP_Admin_Bar $bar)
    {
        if (!is_super_admin() || is_admin() || !is_admin_bar_showing()) {
            return;
        }

        $count = count($this->errors);
        $metaTitle = sprintf(_n('1 error detected during this page load', '%s errors detected during this page load', $count, 'helthe'), $count);

        $bar->add_node(array(
            'id'     => 'helthe',
            'title'  => sprintf(_n('1 Error', '%s Errors', $count, 'helthe'), $count),
            'meta'   => array('title' => $metaTitle),
            'parent' => 'top-secondary'
        ));

        foreach ($this->errors as $i => $error) {
            $bar->add_node(array(
                'id'     => 'helthe_' . $i,
                'title'  => $error,
                'parent' => 'helthe'
            ));
        }
    }

    /**
     * Collect an error message.
     *
     * @param string $message
     */
    public function collectError($message)
    {
        $this->errors[] = $message;
    }
}
