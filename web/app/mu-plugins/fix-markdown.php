<?php
/**
 * Remove problematic hook that escapes html in code blocks
 */
add_action('init', function() {
    if (!class_exists('WPCom_Markdown')) {
        return;
    }

    $markdown = WPCom_Markdown::get_instance();

    remove_filter('content_save_pre', array($markdown, 'preserve_code_blocks'), 1);
}, 99);