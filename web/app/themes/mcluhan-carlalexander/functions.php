<?php

function mcluhan_load_style()
{
    if (is_admin()) {
        return;
    }

    $dependencies = array();

    wp_register_style('mcluhan-fonts', '//fonts.googleapis.com/css?family=Nunito+Sans:400,400i,600,600i,700,700i&amp;display=swap&amp;subset=latin-ext', false, 1.0, 'all');
    $dependencies[] = 'mcluhan-fonts';

    wp_register_style('fontawesome', get_template_directory_uri().'/assets/css/font-awesome.css', null);
    $dependencies[] = 'fontawesome';

    wp_enqueue_style('mcluhan-style', get_template_directory_uri().'/style.css', $dependencies);
    wp_enqueue_style('carlalexander-style', get_stylesheet_directory_uri().'/style.css', array('mcluhan-style'));

    if (is_single()) {
        wp_enqueue_style('highlight-style', get_stylesheet_directory_uri().'/assets/css/highlight.min.css');
    }
}

function mcluhan_add_arrow_main_menu($item_output, $item, $depth, $args)
{
    if (false === stripos($item_output, 'free course') || empty($args->theme_location) || 'main-menu' != $args->theme_location) {
        return $item_output;
    }

    return '<div class="arrow"></div>'.$item_output;
}
add_filter('walker_nav_menu_start_el','mcluhan_add_arrow_main_menu', 10, 4);

function mcluhan_enqueue_headerbar_script()
{
    if (is_front_page() || is_single()) {
        wp_enqueue_script('child-script-headerbar', get_stylesheet_directory_uri() . '/assets/js/headerbar.js', array('jquery'));
    }
    if (is_single()) {
        wp_enqueue_script('child-script-highlight', get_stylesheet_directory_uri() . '/assets/js/highlight.js');
    }
}
add_action('wp_enqueue_scripts', 'mcluhan_enqueue_headerbar_script');

function mcluhan_output_seo()
{
    if (!function_exists('\The_SEO_Framework\_init_tsf'))
        return;

    $tsf =  \The_SEO_Framework\_init_tsf();

    if (!$tsf instanceof \The_SEO_Framework\Init)
        return;

    $tsf->html_output();
}

function mcluhan_output_analytics()
{
    if (!function_exists('monsterinsights_tracking_script'))
        return;

    monsterinsights_tracking_script();
}

function mcluhan_search_only_posts(WP_Query $query)
{
    if (!is_admin() && $query->is_main_query() && $query->is_search()) {
        $query->set('post_type', 'post');
    }
}
add_action('pre_get_posts', 'mcluhan_search_only_posts');

function mcluhan_remove_actions()
{
    remove_action('pre_get_posts', 'mcluhan_sort_search_posts_by_date');
}
add_action('after_setup_theme', 'mcluhan_remove_actions');

function mcluhan_promote_course()
{
    if (is_front_page()) {
        return true;
    }

    $tags = wp_get_post_tags(get_the_ID());

    if (!is_array($tags) || empty($tags)) {
        return false;
    }

    return 2 === count(array_filter($tags, function (WP_Term $tag) {
        return in_array($tag->slug, ['object-oriented-programming', 'wordpress']);
    }));
}
