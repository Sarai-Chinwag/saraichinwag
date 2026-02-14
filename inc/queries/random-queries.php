<?php 
/**
 * High-performance random query modification for home and archive pages
 * Replaces expensive orderby => 'rand' with cached post ID system
 */
function sarai_chinwag_random_home_archive_search_query( $query ) {
    if ( ! is_admin() && $query->is_main_query() && ! $query->is_feed() && ( $query->is_home() || $query->is_archive() || $query->is_search() ) ) {
        // Build post types array
        $post_types = array( 'post' );
        if ( !sarai_chinwag_recipes_disabled() ) {
            $post_types[] = 'recipe';
        }
        if ( !sarai_chinwag_quizzes_disabled() ) {
            $post_types[] = 'quiz';
        }
        if ( !sarai_chinwag_journals_disabled() ) {
            $post_types[] = 'journal';
        }
        
        $query->set( 'post_type', $post_types );
        
        // For home page, use cached random IDs for performance
        if ( $query->is_home() ) {
            // Get posts per page setting
            $posts_per_page = get_option('posts_per_page');
            if ( !$posts_per_page ) {
                $posts_per_page = 10; // Default to 10 if the option is not set
            }
            
            // Use cached random post IDs instead of expensive orderby => 'rand'
            $random_post_ids = sarai_chinwag_get_cached_random_query_ids( $post_types, $posts_per_page );
            
            if ( !empty($random_post_ids) ) {
                $query->set( 'post__in', $random_post_ids );
                $query->set( 'orderby', 'post__in' ); // Maintain the random order from our cached array
            } else {
                // Fallback to random order if no cached IDs available
                $query->set( 'orderby', 'rand' );
            }
            
            $query->set( 'posts_per_page', $posts_per_page );
        } 
        // For archive and search pages, use simple random order to respect filtering
        elseif ( $query->is_archive() || $query->is_search() ) {
            $query->set( 'orderby', 'rand' );
            // Don't override post__in here - let WordPress handle category/tag/search filtering
        }
    }
}
add_action( 'pre_get_posts', 'sarai_chinwag_random_home_archive_search_query' );


