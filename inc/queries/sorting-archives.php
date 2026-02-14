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
            'ajaxurl' => admin_url('admin-ajax.php'),
            'nonce' => wp_create_nonce('filter_posts_nonce'),
            'posts_per_page' => get_option('posts_per_page', 10)
        ));

        wp_enqueue_script('sarai-chinwag-load-more', get_template_directory_uri() . '/js/load-more.js', array('sarai-chinwag-filter-bar'), filemtime(get_template_directory() . '/js/load-more.js'), true);
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

    // Get sort parameter
    $sort_by = isset($_POST['sort_by']) ? sanitize_text_field($_POST['sort_by']) : 'random';
    
    // Get post type filters
    $post_type_filter = isset($_POST['post_type_filter']) ? sanitize_text_field($_POST['post_type_filter']) : 'all';
    
    // Determine post types to query
    $post_types = array();
    if ($post_type_filter === 'posts') {
        $post_types = array('post');
    } elseif ($post_type_filter === 'recipes') {
        $post_types = array('recipe');
    } elseif ($post_type_filter === 'quizzes') {
        $post_types = array('quiz');
    } elseif ($post_type_filter === 'journals') {
        $post_types = array('journal');
    } else { // default and "all"
        $post_types = array('post');
        if (!sarai_chinwag_recipes_disabled()) {
            $post_types[] = 'recipe';
        }
        if (!sarai_chinwag_quizzes_disabled()) {
            $post_types[] = 'quiz';
        }
        if (!sarai_chinwag_journals_disabled()) {
            $post_types[] = 'journal';
        }
    }

    $loaded_posts = isset($_POST['loadedPosts']) ? json_decode(stripslashes($_POST['loadedPosts']), true) : array();
    $category = isset($_POST['category']) ? sanitize_text_field($_POST['category']) : '';
    $tag = isset($_POST['tag']) ? sanitize_text_field($_POST['tag']) : '';
    $search = isset($_POST['search']) ? sanitize_text_field($_POST['search']) : '';

    // Retrieve the posts per page setting from the admin
    $posts_per_page = get_option('posts_per_page', 10);

    // Build query arguments based on sort type
    $args = array(
        'post_type' => $post_types,
        'posts_per_page' => $posts_per_page,
        'post_status' => 'publish',
        'post__not_in' => $loaded_posts,
    );

    // Add category/tag/search constraints
    if ($category) {
        $args['category_name'] = $category;
    }
    if ($tag) {
        $args['tag'] = $tag;
    }
    if ($search) {
        $args['s'] = $search;
    }

    // Apply sorting based on sort_by parameter
    switch ($sort_by) {
        case 'popular':
            $args['meta_key'] = '_post_views';
            $args['orderby'] = 'meta_value_num date';
            $args['order'] = 'DESC';
            $args['meta_query'] = array(
                array(
                    'key' => '_post_views',
                    'compare' => 'EXISTS'
                )
            );
            break;
            
        case 'recent':
            $args['orderby'] = 'date';
            $args['order'] = 'DESC';
            break;
            
        case 'oldest':
            $args['orderby'] = 'date';
            $args['order'] = 'ASC';
            break;
            
        case 'random':
        default:
            $args['orderby'] = 'rand';
            break;
    }

    // Execute the query
    $query = new WP_Query($args);

    if ($query->have_posts()) {
        while ($query->have_posts()) : $query->the_post();
            echo '<article id="post-' . get_the_ID() . '" class="' . join(' ', get_post_class()) . '">';
            get_template_part('template-parts/content', get_post_type());
            echo '</article>';
        endwhile;
        wp_reset_postdata();
    } else {
        echo '<p>' . esc_html__('No posts found.', 'sarai-chinwag') . '</p>';
    }

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

    // Get sort parameter
    $sort_by = isset($_POST['sort_by']) ? sanitize_text_field($_POST['sort_by']) : 'random';
    
    // Get post type filters
    $post_type_filter = isset($_POST['post_type_filter']) ? sanitize_text_field($_POST['post_type_filter']) : 'all';
    
    $loaded_images = isset($_POST['loadedImages']) ? json_decode(stripslashes($_POST['loadedImages']), true) : array();
    $category = isset($_POST['category']) ? sanitize_text_field($_POST['category']) : '';
    $tag = isset($_POST['tag']) ? sanitize_text_field($_POST['tag']) : '';
    $search = isset($_POST['search']) ? sanitize_text_field($_POST['search']) : '';

    // Retrieve the posts per page setting from the admin (batch size)
    $posts_per_page = get_option('posts_per_page', 10);

    // Check if this is a site-wide image gallery request
    $is_all_site = isset($_POST['all_site']) && $_POST['all_site'] === 'true';
    
    if ($is_all_site) {
        // Site-wide image gallery
        $images = sarai_chinwag_get_filtered_all_site_images($sort_by, $post_type_filter, $loaded_images, $posts_per_page);
    } elseif ($search) {
        // Search image gallery
        $images = sarai_chinwag_get_filtered_search_images($search, $sort_by, $post_type_filter, $loaded_images, $posts_per_page);
    } else {
        // Get current term information
        $term = null;
        $term_type = '';
        
        if ($category) {
            $term = get_term_by('slug', $category, 'category');
            $term_type = 'category';
        } elseif ($tag) {
            $term = get_term_by('slug', $tag, 'post_tag');
            $term_type = 'post_tag';
        }
        
        if (!$term) {
            echo '<p>' . esc_html__('No images found.', 'sarai-chinwag') . '</p>';
            wp_die();
        }
        
        // Get images from the term with sorting
        $images = sarai_chinwag_get_filtered_term_images($term->term_id, $term_type, $sort_by, $post_type_filter, $loaded_images, $posts_per_page);
    }
    
    if (empty($images)) {
        echo '<p>' . esc_html__('No more images found.', 'sarai-chinwag') . '</p>';
    } else {
        foreach ($images as $index => $image) {
            include(get_template_directory() . '/template-parts/gallery-item.php');
        }
    }

    wp_die();
}
add_action('wp_ajax_filter_images', 'sarai_chinwag_filter_images');
add_action('wp_ajax_nopriv_filter_images', 'sarai_chinwag_filter_images');
