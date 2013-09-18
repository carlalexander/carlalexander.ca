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
 * This is a proxy class around the wpdb class. It checks for database errors.
 *
 * @author Carl Alexander
 */
class Helthe_Proxy_WPDB extends wpdb
{
    /**
     * Initializes the proxy. Replacing the current wpdb global.
     */
    public static function initialize()
    {
        global $wpdb;

        $wpdb = new self(DB_USER, DB_PASSWORD, DB_NAME, DB_HOST);
        wp_set_wpdb_vars();
    }

    /**
     * {@inheritdoc}
     */
    public function print_error($str = '')
    {
        // Need to get the string ourselves if not given
        if (!$str) {
            $str = mysql_error($this->dbh);
        }

        do_action('helthe_wpdb_database_error', sprintf(__('WordPress database error %1$s for query %2$s', 'helthe'), $str, $this->last_query));

        return parent::print_error($str);
    }
}
