<?php
/**
 * Async View Counter System
 *
 * Tracks post views via REST API after page load to avoid blocking render.
 * Uses sessionStorage on client to prevent duplicate counts within same session.
 *
 * @package Sarai_Chinwag
 * @since 2.0
 */

/**
 * Register REST API endpoint for view tracking
 */
function sarai_chinwag_register_view_counter_endpoint() {
    register_rest_route('sarai-chinwag/v1', '/track-view', array(
        'methods'             => 'POST',
        'callback'            => 'sarai_chinwag_handle_view_track',
        'permission_callback' => '__return_true',
        'args'                => array(
            'post_id' => array(
                'required'          => true,
                'validate_callback' => function($param) {
                    return is_numeric($param) && $param > 0;
                },
                'sanitize_callback' => 'absint',
            ),
        ),
    ));
}
add_action('rest_api_init', 'sarai_chinwag_register_view_counter_endpoint');

/**
 * Handle view tracking request
 *
 * @param WP_REST_Request $request
 * @return WP_REST_Response
 */
function sarai_chinwag_handle_view_track($request) {
    $post_id = $request->get_param('post_id');
    
    $post = get_post($post_id);
    if (!$post || !in_array($post->post_type, array('post', 'recipe', 'quiz', 'journal'), true)) {
        return new WP_REST_Response(array('success' => false), 400);
    }
    
    if ($post->post_status !== 'publish') {
        return new WP_REST_Response(array('success' => false), 400);
    }
    
    $views = get_post_meta($post_id, '_post_views', true);
    $views = $views ? intval($views) : 0;
    
    update_post_meta($post_id, '_post_views', $views + 1);
    
    return new WP_REST_Response(array('success' => true), 200);
}

/**
 * Enqueue view counter script on single posts
 */
function sarai_chinwag_enqueue_view_counter() {
    if (!is_singular(array('post', 'recipe', 'quiz', 'journal'))) {
        return;
    }
    
    global $post;
    if (!$post) {
        return;
    }
    
    $theme_dir = get_template_directory();
    $theme_uri = get_template_directory_uri();
    
    $version = filemtime($theme_dir . '/inc/assets/js/view-counter.js');
    
    wp_enqueue_script(
        'sarai-chinwag-view-counter',
        $theme_uri . '/inc/assets/js/view-counter.js',
        array(),
        $version,
        true
    );
    
    wp_localize_script('sarai-chinwag-view-counter', 'saraiViewCounter', array(
        'postId'  => $post->ID,
        'restUrl' => rest_url('sarai-chinwag/v1/track-view'),
        'nonce'   => wp_create_nonce('wp_rest'),
    ));
}
add_action('wp_enqueue_scripts', 'sarai_chinwag_enqueue_view_counter');

/**
 * Get view count for a post
 *
 * @param int $post_id
 * @return int
 */
function sarai_chinwag_get_post_views($post_id) {
    $views = get_post_meta($post_id, '_post_views', true);
    return $views ? intval($views) : 0;
}

/**
 * Display formatted view count
 *
 * @param int $post_id
 * @return string
 */
function sarai_chinwag_display_post_views($post_id) {
    $views = sarai_chinwag_get_post_views($post_id);
    
    if ($views === 0) {
        return __('No views yet', 'sarai-chinwag');
    } elseif ($views === 1) {
        return __('1 view', 'sarai-chinwag');
    }
    
    return sprintf(__('%s views', 'sarai-chinwag'), number_format($views));
}

/**
 * Get most popular posts by view count
 *
 * @param array $args WP_Query arguments
 * @return WP_Query
 */
function sarai_chinwag_get_popular_posts($args = array()) {
    $default_args = array(
        'meta_key'   => '_post_views',
        'orderby'    => 'meta_value_num date',
        'order'      => 'DESC',
        'meta_query' => array(
            array(
                'key'     => '_post_views',
                'value'   => 0,
                'compare' => '>',
                'type'    => 'NUMERIC',
            ),
        ),
    );
    
    return new WP_Query(array_merge($default_args, $args));
}

/**
 * Initialize view count for posts without meta (admin only)
 *
 * Runs once per day in admin to backfill posts missing view count meta.
 */
function sarai_chinwag_initialize_post_views() {
    if (!is_admin()) {
        return;
    }
    
    if (wp_cache_get('views_initialized', 'sarai_chinwag_views')) {
        return;
    }
    
    global $wpdb;
    
    $posts_without_views = $wpdb->get_results("
        SELECT p.ID 
        FROM {$wpdb->posts} p 
        LEFT JOIN {$wpdb->postmeta} pm ON p.ID = pm.post_id AND pm.meta_key = '_post_views'
        WHERE p.post_status = 'publish' 
        AND p.post_type IN ('post', 'recipe', 'quiz', 'journal')
        AND pm.meta_id IS NULL
        LIMIT 100
    ");
    
    foreach ($posts_without_views as $post) {
        update_post_meta($post->ID, '_post_views', 0);
    }
    
    wp_cache_set('views_initialized', true, 'sarai_chinwag_views', DAY_IN_SECONDS);
}
add_action('admin_init', 'sarai_chinwag_initialize_post_views');
