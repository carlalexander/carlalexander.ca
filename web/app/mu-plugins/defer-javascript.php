<?php

add_filter('script_loader_tag', function ($url) {
    if (
        (is_user_logged_in() && is_admin())
        || false === strpos($url, '.js')
        || false !== strpos($url, 'jquery.js')
    ) {
        return $url;
    }

    return str_replace(' src', ' defer src', $url);
}, 10);
