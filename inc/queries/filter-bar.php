<?php
/**
 * Filter Bar System
 *
 * Displays filter bar on home, archive, search, and image gallery pages
 *
 * @package Sarai_Chinwag
 * @since 2.0
 */

function sarai_chinwag_display_filter_bar() {
    $is_image_gallery = function_exists('sarai_chinwag_is_image_mode') && sarai_chinwag_is_image_mode();
    
    if (!is_home() && !is_archive() && !is_search() && !$is_image_gallery) {
        return;
    }

    // Don't show filter bar on journal archives — journals have their own layout.
    if (is_post_type_archive('journal')) {
        return;
    }
    
    get_template_part('template-parts/filter', 'bar');
}

function sarai_chinwag_show_type_filters() {
    // Show filters if there are multiple post types available
    $available_types = array('post');
    
    if (!sarai_chinwag_recipes_disabled()) {
        $available_types[] = 'recipe';
    }
    
    if (!sarai_chinwag_quizzes_disabled()) {
        $available_types[] = 'quiz';
    }
    
    return count($available_types) > 1;
}

add_action('before_post_grid', 'sarai_chinwag_display_filter_bar');
