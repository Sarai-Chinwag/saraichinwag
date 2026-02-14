<?php
/**
 * Search Image Extraction System
 * 
 * Extracts all images from posts matching search queries
 * for display in search image gallery archives.
 *
 * @package Sarai_Chinwag
 */

/**
 * Extract all images from posts matching a search query
 *
 * Count functions use high limit (99999), display functions use default (30).
 * Results cached for 1 hour with MD5 hash of query as cache key.
 *
 * @param string $search_query The search query
 * @param int    $limit        Maximum images (99999 for counts, 30 for display)
 * @return array Array of image data
 * @since 2.1
 */
function sarai_chinwag_extract_images_from_search($search_query, $limit = 30) {
    // Check cache first
    $cache_key = "sarai_chinwag_search_images_" . md5($search_query);
    $cached_images = wp_cache_get($cache_key, 'sarai_chinwag_images');
    
    if ($cached_images !== false) {
        return array_slice($cached_images, 0, $limit);
    }
    
    // Get posts from search query
    $posts = get_posts(array(
        'post_type' => array('post', 'recipe', 'quiz'),
        'post_status' => 'publish',
        'numberposts' => 500, // Limit posts to prevent memory issues
        'orderby' => 'rand',
        's' => $search_query,
        'meta_query' => array(
            'relation' => 'OR',
            array(
                'key' => '_thumbnail_id',
                'compare' => 'EXISTS'
            ),
            array(
                'key' => '_thumbnail_id',
                'compare' => 'NOT EXISTS'
            )
        )
    ));
    
    $all_images = array();
    $seen_attachments = array(); // Prevent duplicates
    
    foreach ($posts as $post) {
        setup_postdata($post);
        
        // Extract images from this post
        $post_images = sarai_chinwag_extract_images_from_post($post->ID);
        
        // Add to collection, avoiding duplicates
        foreach ($post_images as $image) {
            $attachment_id = $image['attachment_id'];
            if (!in_array($attachment_id, $seen_attachments)) {
                $seen_attachments[] = $attachment_id;
                $all_images[] = $image;
            }
        }
        
        // Stop if we've found enough images
        if (count($all_images) >= $limit * 2) {
            break;
        }
    }
    
    wp_reset_postdata();
    
    // Shuffle images for true randomization at the image level (not just random posts)
    // This aligns initial page load behavior with AJAX filter randomization
    shuffle($all_images);
    
    // Cache the results for 1 hour
    wp_cache_set($cache_key, $all_images, 'sarai_chinwag_images', 3600);
    
    return array_slice($all_images, 0, $limit);
}

/**
 * Get filtered and sorted images from posts matching a search query
 *
 * Used for AJAX filtering on search image gallery pages.
 * Supports sort methods, post type filtering, and duplicate prevention.
 *
 * @param string $search_query     The search query
 * @param string $sort_by          Sort method (random, recent, oldest, popular)
 * @param string $post_type_filter Filter by post type (all, posts, recipes)
 * @param array  $loaded_images    Already loaded image attachment IDs
 * @param int    $limit            Maximum number of images to return
 * @return array Array of image data
 * @since 2.1
 */
function sarai_chinwag_get_filtered_search_images($search_query, $sort_by = 'random', $post_type_filter = 'all', $loaded_images = array(), $limit = 30) {
    // Determine post types to include
    $post_types = sarai_chinwag_resolve_post_types( $post_type_filter );
    
    // Get posts from search query with appropriate sorting
    $post_args = array(
        'post_type' => $post_types,
        'post_status' => 'publish',
        'numberposts' => 500, // Limit posts to prevent memory issues
        's' => $search_query
    );
    
    // Apply sorting to posts (this affects the order of image extraction)
    switch ($sort_by) {
        case 'popular':
            $post_args['meta_key'] = '_post_views';
            $post_args['orderby'] = 'meta_value_num date';
            $post_args['order'] = 'DESC';
            $post_args['meta_query'] = array(
                array(
                    'key' => '_post_views',
                    'compare' => 'EXISTS'
                )
            );
            break;
            
        case 'recent':
            $post_args['orderby'] = 'date';
            $post_args['order'] = 'DESC';
            break;
            
        case 'oldest':
            $post_args['orderby'] = 'date';
            $post_args['order'] = 'ASC';
            break;
            
        case 'random':
        default:
            $post_args['orderby'] = 'rand';
            break;
    }
    
    $posts = get_posts($post_args);
    
    $all_images = array();
    $seen_attachments = array(); // Prevent duplicates
    
    // Include already loaded images in seen list
    $seen_attachments = array_merge($seen_attachments, $loaded_images);
    
    foreach ($posts as $post) {
        setup_postdata($post);
        
        // Extract images from this post
        $post_images = sarai_chinwag_extract_images_from_post($post->ID);
        
        // Add to collection, avoiding duplicates and loaded images
        foreach ($post_images as $image) {
            $attachment_id = $image['attachment_id'];
            if (!in_array($attachment_id, $seen_attachments)) {
                $seen_attachments[] = $attachment_id;
                $all_images[] = $image;
            }
        }
        
        // Stop if we've found enough images
        if (count($all_images) >= $limit * 2) {
            break;
        }
    }
    
    wp_reset_postdata();
    
    // If sorting by random, shuffle the final image collection
    // This ensures truly random images rather than random posts with sequential images
    if ($sort_by === 'random') {
        shuffle($all_images);
    }
    
    return array_slice($all_images, 0, $limit);
}

/**
 * Clear search image cache when posts are updated
 *
 * Clears data cache for post title search and delegates count cache clearing.
 * Search count caches use MD5 hashes of query strings, preventing individual invalidation.
 * These caches expire naturally after 2 hours.
 *
 * @param int $post_id Post ID
 * @since 2.1
 * @since 2.2.1 Updated to use centralized count cache clearing
 */
function sarai_chinwag_clear_search_image_cache_on_post_update($post_id) {
    $cache_key = "sarai_chinwag_search_images_" . md5(get_the_title($post_id));
    wp_cache_delete($cache_key, 'sarai_chinwag_images');

    if (function_exists('sarai_chinwag_clear_all_image_count_caches')) {
        sarai_chinwag_clear_all_image_count_caches($post_id);
    }
}
add_action('save_post', 'sarai_chinwag_clear_search_image_cache_on_post_update');
add_action('delete_post', 'sarai_chinwag_clear_search_image_cache_on_post_update');