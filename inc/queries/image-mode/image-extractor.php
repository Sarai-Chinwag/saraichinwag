<?php
/**
 * Image Extractor System
 *
 * Extracts images from posts for gallery archives
 *
 * @package Sarai_Chinwag
 * @since 2.1
 */

/**
 * Extract images from posts in specific term
 *
 * Count functions use high limit (99999), display functions use default (30).
 * Results cached for 1 hour in sarai_chinwag_images cache group.
 *
 * @param int    $term_id   Term ID
 * @param string $term_type Taxonomy
 * @param int    $limit     Maximum images (99999 for counts, 30 for display)
 * @return array Array of image data
 * @since 2.1
 */
function sarai_chinwag_extract_images_from_term($term_id, $term_type, $limit = 30) {
    // Use separate cache keys for display (small limit) vs counting (large limit)
    $is_count_request = $limit > 1000;
    $cache_key = $is_count_request 
        ? "sarai_chinwag_term_images_{$term_id}_{$term_type}_full"
        : "sarai_chinwag_term_images_{$term_id}_{$term_type}";
    $cached_images = wp_cache_get($cache_key, 'sarai_chinwag_images');
    
    // For count requests, only use cache if it has enough images
    if ($cached_images !== false) {
        if (!$is_count_request || count($cached_images) >= $limit) {
            return array_slice($cached_images, 0, $limit);
        }
    }
    
    // For counting, get ALL posts; for display, limit to 500
    $query_limit = $is_count_request ? -1 : 500;
    
    $posts = get_posts(array(
        'post_type' => array('post', 'recipe', 'quiz', 'journal'),
        'post_status' => 'publish',
        'numberposts' => $query_limit,
        'orderby' => $is_count_request ? 'ID' : 'rand',
        'tax_query' => array(
            array(
                'taxonomy' => $term_type,
                'field' => 'term_id',
                'terms' => $term_id,
            )
        ),
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
    $seen_attachments = array();
    
    foreach ($posts as $post) {
        setup_postdata($post);
        
        $post_images = sarai_chinwag_extract_images_from_post($post->ID);
        
        foreach ($post_images as $image) {
            $attachment_id = $image['attachment_id'];
            if (!in_array($attachment_id, $seen_attachments)) {
                $seen_attachments[] = $attachment_id;
                $all_images[] = $image;
            }
        }
        
        // Only break early for display requests
        if (!$is_count_request && count($all_images) >= $limit * 2) {
            break;
        }
    }
    
    wp_reset_postdata();
    
    if (!$is_count_request) {
        shuffle($all_images);
    }
    
    // Cache with appropriate TTL (longer for counts since they're expensive)
    $ttl = $is_count_request ? DAY_IN_SECONDS : 3600;
    wp_cache_set($cache_key, $all_images, 'sarai_chinwag_images', $ttl);
    
    return $is_count_request ? $all_images : array_slice($all_images, 0, $limit);
}

/**
 * Extract images from single post
 *
 * @param int $post_id Post ID
 * @return array Image data
 * @since 2.1
 */
function sarai_chinwag_extract_images_from_post($post_id) {
    $images = array();

    $featured_image_id = get_post_thumbnail_id($post_id);
    if ($featured_image_id) {
        $image_data = sarai_chinwag_get_image_data($featured_image_id, $post_id, 'featured');
        if ($image_data) {
            $images[] = $image_data;
        }
    }

    $post = get_post($post_id);
    if (!$post) {
        return $images;
    }

    $content = $post->post_content;

    if (has_blocks($content)) {
        $blocks = parse_blocks($content);
        $block_images = sarai_chinwag_extract_images_from_blocks($blocks, $post_id);
        $images = array_merge($images, $block_images);
    }

    return $images;
}

/**
 * Extract images from Gutenberg blocks
 *
 * @param array $blocks Block data
 * @param int $post_id Post ID
 * @return array Image data
 * @since 2.1
 */
function sarai_chinwag_extract_images_from_blocks($blocks, $post_id) {
    $images = array();

    foreach ($blocks as $block) {
        if ($block['blockName'] === 'core/image' && isset($block['attrs']['id'])) {
            $attachment_id = $block['attrs']['id'];
            $image_data = sarai_chinwag_get_image_data($attachment_id, $post_id, 'content');
            if ($image_data) {
                $images[] = $image_data;
            }
        }

        if ($block['blockName'] === 'core/gallery' && isset($block['attrs']['ids'])) {
            foreach ($block['attrs']['ids'] as $attachment_id) {
                $image_data = sarai_chinwag_get_image_data($attachment_id, $post_id, 'gallery');
                if ($image_data) {
                    $images[] = $image_data;
                }
            }
        }

        if ($block['blockName'] === 'core/media-text' && isset($block['attrs']['mediaId'])) {
            $attachment_id = $block['attrs']['mediaId'];
            $image_data = sarai_chinwag_get_image_data($attachment_id, $post_id, 'media-text');
            if ($image_data) {
                $images[] = $image_data;
            }
        }

        if (!empty($block['innerBlocks'])) {
            $nested_images = sarai_chinwag_extract_images_from_blocks($block['innerBlocks'], $post_id);
            $images = array_merge($images, $nested_images);
        }
    }

    return $images;
}

/**
 * Get formatted image data for gallery display
 *
 * @param int $attachment_id Attachment ID
 * @param int $post_id Post ID
 * @param string $source Image source
 * @return array|false Image data or false
 * @since 2.1
 */
function sarai_chinwag_get_image_data($attachment_id, $post_id, $source = 'content') {
    $attachment = get_post($attachment_id);
    if (!$attachment || $attachment->post_type !== 'attachment') {
        return false;
    }
    
    $image_meta = wp_get_attachment_metadata($attachment_id);
    if (!$image_meta) {
        return false;
    }
    
    $full_url = wp_get_attachment_url($attachment_id);
    $thumb_url = wp_get_attachment_image_url($attachment_id, 'grid-thumb');
    $medium_url = wp_get_attachment_image_url($attachment_id, 'medium_large');
    
    $source_post = get_post($post_id);
    
    return array(
        'attachment_id' => $attachment_id,
        'post_id' => $post_id,
        'source' => $source,
        'title' => get_the_title($attachment_id) ?: $source_post->post_title,
        'alt' => get_post_meta($attachment_id, '_wp_attachment_image_alt', true),
        'caption' => $attachment->post_excerpt,
        'description' => $attachment->post_content,
        'url_full' => $full_url,
        'url_thumb' => $thumb_url,
        'url_medium' => $medium_url,
        'width' => $image_meta['width'] ?? 0,
        'height' => $image_meta['height'] ?? 0,
        'source_post_title' => $source_post->post_title,
        'source_post_url' => get_permalink($post_id),
        'source_post_date' => $source_post->post_date,
    );
}

/**
 * Clear image caches when posts are updated
 *
 * Clears both count caches (via centralized function) and data caches.
 * Count caches: accurate image counts for archive image mode links.
 * Data caches: actual image arrays for gallery display.
 *
 * @param int $post_id Post ID
 * @since 2.1
 * @since 2.2.1 Updated to use centralized count cache clearing
 */
function sarai_chinwag_clear_image_cache_on_post_update($post_id) {
    if (function_exists('sarai_chinwag_clear_all_image_count_caches')) {
        sarai_chinwag_clear_all_image_count_caches($post_id);
    }

    $categories = get_the_category($post_id);
    $tags = get_the_tags($post_id);

    if ($categories) {
        foreach ($categories as $category) {
            wp_cache_delete("sarai_chinwag_term_images_{$category->term_id}_category", 'sarai_chinwag_images');
        }
    }

    if ($tags) {
        foreach ($tags as $tag) {
            wp_cache_delete("sarai_chinwag_term_images_{$tag->term_id}_post_tag", 'sarai_chinwag_images');
        }
    }

    wp_cache_delete('sarai_chinwag_all_site_images', 'sarai_chinwag_images');
}
add_action('save_post', 'sarai_chinwag_clear_image_cache_on_post_update');
add_action('delete_post', 'sarai_chinwag_clear_image_cache_on_post_update');

/**
 * Get filtered and sorted images from term posts for AJAX
 *
 * @param int    $term_id         Term ID
 * @param string $term_type       Taxonomy
 * @param string $sort_by         Sort method
 * @param string $post_type_filter Post type filter
 * @param array  $loaded_images   Loaded attachment IDs
 * @param int    $limit           Maximum images
 * @return array Image data
 * @since 2.1
 */
function sarai_chinwag_get_filtered_term_images($term_id, $term_type, $sort_by = 'random', $post_type_filter = 'all', $loaded_images = array(), $limit = 30) {
    $post_types = array('post');
    if (!sarai_chinwag_recipes_disabled()) {
        if ($post_type_filter === 'recipes') {
            $post_types = array('recipe');
        } elseif ($post_type_filter === 'all') {
            $post_types[] = 'recipe';
        }
    }
    if (!sarai_chinwag_quizzes_disabled()) {
        if ($post_type_filter === 'quizzes') {
            $post_types = array('quiz');
        } elseif ($post_type_filter === 'all') {
            $post_types[] = 'quiz';
        }
    }
    if (!sarai_chinwag_journals_disabled()) {
        if ($post_type_filter === 'journals') {
            $post_types = array('journal');
        } elseif ($post_type_filter === 'all') {
            $post_types[] = 'journal';
        }
    }
    
    $post_args = array(
        'post_type' => $post_types,
        'post_status' => 'publish',
        'numberposts' => 500, // Limit posts to prevent memory issues
        'tax_query' => array(
            array(
                'taxonomy' => $term_type,
                'field' => 'term_id',
                'terms' => $term_id,
            )
        )
    );
    
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
    $seen_attachments = array();
    
    $seen_attachments = array_merge($seen_attachments, $loaded_images);
    
    foreach ($posts as $post) {
        setup_postdata($post);
        
        // Extract images from this post
        $post_images = sarai_chinwag_extract_images_from_post($post->ID);
        
        // Add to collection and loaded images
        foreach ($post_images as $image) {
            $attachment_id = $image['attachment_id'];
            if (!in_array($attachment_id, $seen_attachments)) {
                $seen_attachments[] = $attachment_id;
                $all_images[] = $image;
            }
        }
        
        // Stop when enough images found
        if (count($all_images) >= $limit * 2) {
            break;
        }
    }
    
    wp_reset_postdata();
    
    if ($sort_by === 'random') {
        shuffle($all_images);
    }
    
    return array_slice($all_images, 0, $limit);
}

/**
 * Get all images from site posts
 *
 * Count functions use high limit (99999), display functions use default (30).
 * Results cached for 1 hour in sarai_chinwag_images cache group.
 *
 * @param int $limit Maximum images (99999 for counts, 30 for display)
 * @return array Image data
 * @since 2.1
 */
function sarai_chinwag_get_all_site_images($limit = 30) {
    // Use separate cache keys for display (small limit) vs counting (large limit)
    $is_count_request = $limit > 1000;
    $cache_key = $is_count_request ? "sarai_chinwag_all_site_images_full" : "sarai_chinwag_all_site_images";
    $cached_images = wp_cache_get($cache_key, 'sarai_chinwag_images');
    
    // For count requests, only use cache if it has enough images
    if ($cached_images !== false) {
        if (!$is_count_request || count($cached_images) >= $limit) {
            return array_slice($cached_images, 0, $limit);
        }
    }
    
    // For counting, query ALL attachments; for display, limit the query
    $query_limit = $is_count_request ? -1 : ($limit * 2);
    
    $args = array(
        'post_type' => 'attachment',
        'post_status' => 'inherit',
        'post_mime_type' => 'image',
        'posts_per_page' => $query_limit,
        'orderby' => $is_count_request ? 'ID' : 'rand',
        'meta_query' => array(
            array(
                'key' => '_wp_attached_file',
                'compare' => 'EXISTS'
            )
        )
    );
    
    $attachments = get_posts($args);
    
    $images = array();
    foreach ($attachments as $attachment) {
        $parent_post = get_post($attachment->post_parent);
        if (!$parent_post || $parent_post->post_status !== 'publish') {
            continue;
        }
        
        if (!in_array($parent_post->post_type, array('post', 'recipe', 'quiz', 'journal'))) {
            continue;
        }
        
        $image_data = sarai_chinwag_get_image_data($attachment->ID, $parent_post->ID, 'attached');
        if ($image_data) {
            $images[] = $image_data;
        }
        
        // Only break early for display requests
        if (!$is_count_request && count($images) >= $limit) {
            break;
        }
    }
    
    if (!$is_count_request) {
        shuffle($images);
    }
    
    // Cache with appropriate TTL (longer for counts since they're expensive)
    $ttl = $is_count_request ? DAY_IN_SECONDS : 3600;
    wp_cache_set($cache_key, $images, 'sarai_chinwag_images', $ttl);
    
    return $is_count_request ? $images : array_slice($images, 0, $limit);
}

/**
 * Get filtered images from all site posts for AJAX
 *
 * @param string $sort_by         Sort method
 * @param string $post_type_filter Post type filter
 * @param array  $loaded_images   Loaded attachment IDs
 * @param int    $limit           Maximum images
 * @return array Image data
 * @since 2.1
 */
function sarai_chinwag_get_filtered_all_site_images($sort_by = 'random', $post_type_filter = 'all', $loaded_images = array(), $limit = 30) {
    // Determine post types to include
    $post_types = array('post');
    if (!sarai_chinwag_recipes_disabled()) {
        if ($post_type_filter === 'recipes') {
            $post_types = array('recipe');
        } elseif ($post_type_filter === 'all') {
            $post_types[] = 'recipe';
        }
    }
    if (!sarai_chinwag_quizzes_disabled()) {
        if ($post_type_filter === 'quizzes') {
            $post_types = array('quiz');
        } elseif ($post_type_filter === 'all') {
            $post_types[] = 'quiz';
        }
    }
    if (!sarai_chinwag_journals_disabled()) {
        if ($post_type_filter === 'journals') {
            $post_types = array('journal');
        } elseif ($post_type_filter === 'all') {
            $post_types[] = 'journal';
        }
    }
    
    $args = array(
        'post_type' => 'attachment',
        'post_status' => 'inherit',
        'post_mime_type' => 'image',
        'posts_per_page' => $limit * 3, // Get extra to account for filtering
        'post__not_in' => $loaded_images,
        'meta_query' => array(
            array(
                'key' => '_wp_attached_file',
                'compare' => 'EXISTS'
            )
        )
    );
    
    switch ($sort_by) {
        case 'recent':
            $args['orderby'] = 'date';
            $args['order'] = 'DESC';
            break;
            
        case 'oldest':
            $args['orderby'] = 'date';
            $args['order'] = 'ASC';
            break;
            
        case 'popular':
            $args['orderby'] = 'rand';
            break;
            
        case 'random':
        default:
            $args['orderby'] = 'rand';
            break;
    }
    
    $attachments = get_posts($args);
    
    $images = array();
    foreach ($attachments as $attachment) {
        $parent_post = get_post($attachment->post_parent);
        if (!$parent_post || $parent_post->post_status !== 'publish') {
            continue;
        }
        
        if (!in_array($parent_post->post_type, $post_types)) {
            continue;
        }
        
        $image_data = sarai_chinwag_get_image_data($attachment->ID, $parent_post->ID, 'attached');
        if ($image_data) {
            $images[] = $image_data;
        }
        
        if (count($images) >= $limit) {
            break;
        }
    }
    
    return $images;
}
