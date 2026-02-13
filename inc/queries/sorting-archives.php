<?php
/**
 * Archive sorting and filtering functionality
 * 
 * Provides AJAX filtering system for posts and images with sort options,
 * content type filtering, and context-aware filtering for categories,
 * tags, and search results. Includes script enqueueing and helper functions.
 *
 * @package Sarai_Chinwag
 * @since 2.0.0
 */

/**
 * Enqueue filter and load-more scripts for archive pages
 * 
 * Conditionally loads filtering JavaScript with AJAX localization
 * on home, archive, search, and image gallery pages with proper
 * script dependencies and dynamic versioning.
 * 
 * @since 2.0.0
 */
function sarai_chinwag_enqueue_filter_scripts() {
    $has_images_var = get_query_var('images') !== false;
    $url_has_images = strpos($_SERVER['REQUEST_URI'], '/images/') !== false || strpos($_SERVER['REQUEST_URI'], '/images') !== false;
    $is_image_gallery = $has_images_var && $url_has_images;
    
    if (is_home() || is_archive() || is_search() || $is_image_gallery) {
        wp_enqueue_script('sarai-chinwag-filter-bar', get_template_directory_uri() . '/js/filter-bar.js', array('sarai-chinwag-gallery-utils'), filemtime(get_template_directory() . '/js/filter-bar.js'), true);
        wp_localize_script('sarai-chinwag-filter-bar', 'sarai_chinwag_ajax', array(
            'restUrl'        => rest_url('wp-abilities/v1/abilities/'),
            'nonce'          => wp_create_nonce('wp_rest'),
            'ajaxurl'        => admin_url('admin-ajax.php'),
            'ajax_nonce'     => wp_create_nonce('filter_posts_nonce'),
            'posts_per_page' => get_option('posts_per_page', 10),
        ));
    }
}
add_action('wp_enqueue_scripts', 'sarai_chinwag_enqueue_filter_scripts');

/**
 * Check if site has both posts and recipes in current query context
 * 
 * Determines whether both post types exist for the current page context
 * to control display of content type filter buttons. Respects recipe
 * disable toggle and provides context-aware checking for different pages.
 * 
 * @return bool True if both posts and recipes exist in current context
 * @since 2.0.0
 */
function sarai_chinwag_has_both_posts_and_recipes() {
    if (sarai_chinwag_recipes_disabled()) {
        return false;
    }
    
    if (is_home()) {
        $has_posts = get_posts(array(
            'post_type' => 'post',
            'post_status' => 'publish',
            'numberposts' => 1,
            'fields' => 'ids'
        ));
        
        $has_recipes = get_posts(array(
            'post_type' => 'recipe',
            'post_status' => 'publish', 
            'numberposts' => 1,
            'fields' => 'ids'
        ));
        
        return !empty($has_posts) && !empty($has_recipes);
    }
    
    if (is_archive() || is_search()) {
        global $wp_query;

        $post_query_args = $wp_query->query_vars;
        $post_query_args['post_type'] = 'post';
        $post_query_args['posts_per_page'] = 1;
        $post_query = new WP_Query($post_query_args);
        $has_posts = $post_query->have_posts();

        $recipe_query_args = $wp_query->query_vars;
        $recipe_query_args['post_type'] = 'recipe';
        $recipe_query_args['posts_per_page'] = 1;
        $recipe_query = new WP_Query($recipe_query_args);
        $has_recipes = $recipe_query->have_posts();

        wp_reset_postdata();
        
        return $has_posts && $has_recipes;
    }

    return false;
}

/**
 * AJAX handler for filtering posts
 * 
 * Handles AJAX requests for filtering posts with sort options (random,
 * popular, recent, oldest) and content type filtering. Includes security
 * verification, input sanitization, and context-aware filtering for
 * categories, tags, and search results.
 * 
 * @since 2.0.0
 */
function sarai_chinwag_filter_posts() {
    check_ajax_referer('filter_posts_nonce', 'nonce');

    $input = array(
        'sort_by'          => isset($_POST['sort_by']) ? sanitize_text_field($_POST['sort_by']) : 'random',
        'post_type_filter' => isset($_POST['post_type_filter']) ? sanitize_text_field($_POST['post_type_filter']) : 'all',
        'loaded_ids'       => isset($_POST['loadedPosts']) ? array_map('absint', json_decode(stripslashes($_POST['loadedPosts']), true)) : array(),
        'category'         => isset($_POST['category']) ? sanitize_text_field($_POST['category']) : '',
        'tag'              => isset($_POST['tag']) ? sanitize_text_field($_POST['tag']) : '',
        'search'           => isset($_POST['search']) ? sanitize_text_field($_POST['search']) : '',
    );

    $result = sarai_chinwag_ability_query_posts($input);
    echo $result['html'] ?: '<p>' . esc_html__('No posts found.', 'sarai-chinwag') . '</p>';
    wp_die();
}
add_action('wp_ajax_filter_posts', 'sarai_chinwag_filter_posts');
add_action('wp_ajax_nopriv_filter_posts', 'sarai_chinwag_filter_posts');

/**
 * AJAX handler for filtering images
 * 
 * Handles AJAX requests for filtering images in gallery mode with sort
 * options and content type filtering. Works with site-wide galleries,
 * category/tag galleries, and search galleries. Includes deduplication
 * logic and proper template inclusion.
 * 
 * @since 2.0.0
 */
function sarai_chinwag_filter_images() {
    check_ajax_referer('filter_posts_nonce', 'nonce');

    $input = array(
        'sort_by'          => isset($_POST['sort_by']) ? sanitize_text_field($_POST['sort_by']) : 'random',
        'post_type_filter' => isset($_POST['post_type_filter']) ? sanitize_text_field($_POST['post_type_filter']) : 'all',
        'loaded_ids'       => isset($_POST['loadedImages']) ? array_map('absint', json_decode(stripslashes($_POST['loadedImages']), true)) : array(),
        'category'         => isset($_POST['category']) ? sanitize_text_field($_POST['category']) : '',
        'tag'              => isset($_POST['tag']) ? sanitize_text_field($_POST['tag']) : '',
        'search'           => isset($_POST['search']) ? sanitize_text_field($_POST['search']) : '',
        'all_site'         => isset($_POST['all_site']) && $_POST['all_site'] === 'true',
    );

    $result = sarai_chinwag_ability_query_images($input);
    echo $result['html'];
    wp_die();
}
add_action('wp_ajax_filter_images', 'sarai_chinwag_filter_images');
add_action('wp_ajax_nopriv_filter_images', 'sarai_chinwag_filter_images');
