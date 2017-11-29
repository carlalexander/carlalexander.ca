<?php

/**
 * Fix issue with uploads URLs pointing to the main site. This causes CORS errors in browsers.
 */
add_filter('upload_dir', function (array $uploads) {
    if (!is_multisite() || 1 === get_current_blog_id()) {
        return $uploads;
    }

    $currentSiteUrl = get_home_url();
    $mainSiteUrl = get_home_url(1);

    $uploads['url'] = str_ireplace($mainSiteUrl, $currentSiteUrl, $uploads['url']);
    $uploads['baseurl'] = str_ireplace($mainSiteUrl, $currentSiteUrl, $uploads['baseurl']);

    return $uploads;
});

/**
 * Fix issue with the plugin URL pointing to the main site. This causes CORS errors in browsers.
 */
add_filter('plugins_url', function ($url) {
    if (!is_multisite() || 1 === get_current_blog_id()) {
        return $url;
    }

    return str_ireplace(get_home_url(1), get_home_url(), $url);
});