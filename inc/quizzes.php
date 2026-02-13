<?php
/**
 * Quiz Post Type Registration
 *
 * Registers quiz custom post type with universal theme toggle
 *
 * @package Sarai_Chinwag
 * @since 2.0
 */

function sarai_chinwag_register_quiz_post_type() {
    if (sarai_chinwag_quizzes_disabled()) {
        return;
    }
    $labels = array(
        'name'                  => _x( 'Quizzes', 'Post type general name', 'sarai-chinwag' ),
        'singular_name'         => _x( 'Quiz', 'Post type singular name', 'sarai-chinwag' ),
        'menu_name'             => _x( 'Quizzes', 'Admin Menu text', 'sarai-chinwag' ),
        'name_admin_bar'        => _x( 'Quiz', 'Add New on Toolbar', 'sarai-chinwag' ),
        'add_new'               => __( 'Add New', 'quiz', 'sarai-chinwag' ),
        'add_new_item'          => __( 'Add New Quiz', 'sarai-chinwag' ),
        'new_item'              => __( 'New Quiz', 'sarai-chinwag' ),
        'edit_item'             => __( 'Edit Quiz', 'sarai-chinwag' ),
        'view_item'             => __( 'View Quiz', 'sarai-chinwag' ),
        'all_items'             => __( 'All Quizzes', 'sarai-chinwag' ),
        'search_items'          => __( 'Search Quizzes', 'sarai-chinwag' ),
        'parent_item_colon'     => __( 'Parent Quizzes:', 'sarai-chinwag' ),
        'not_found'             => __( 'No quizzes found.', 'sarai-chinwag' ),
        'not_found_in_trash'    => __( 'No quizzes found in Trash.', 'sarai-chinwag' ),
        'featured_image'        => _x( 'Quiz Cover Image', 'Overrides the "Featured Image" phrase for this post type. Added in 4.3', 'sarai-chinwag' ),
        'set_featured_image'    => _x( 'Set cover image', 'Overrides the "Set featured image" phrase for this post type. Added in 4.3', 'sarai-chinwag' ),
        'remove_featured_image' => _x( 'Remove cover image', 'Overrides the "Remove featured image" phrase for this post type. Added in 4.3', 'sarai-chinwag' ),
        'use_featured_image'    => _x( 'Use as cover image', 'Overrides the "Use as featured image" phrase for this post type. Added in 4.3', 'sarai-chinwag' ),
        'archives'              => _x( 'Quiz archives', 'The post type archive label used in nav menus. Default "Post Archives". Added in 4.4', 'sarai-chinwag' ),
        'insert_into_item'      => _x( 'Insert into quiz', 'Overrides the "Insert into post"/"Insert into page" phrase (used when inserting media into a post). Added in 4.4', 'sarai-chinwag' ),
        'uploaded_to_this_item' => _x( 'Uploaded to this quiz', 'Overrides the "Uploaded to this post"/"Uploaded to this page" phrase (used when viewing media attached to a post). Added in 4.4', 'sarai-chinwag' ),
        'filter_items_list'     => _x( 'Filter quizzes list', 'Screen reader text for the filter links heading on the post type listing screen. Default "Filter posts list"/"Filter pages list". Added in 4.4', 'sarai-chinwag' ),
        'items_list_navigation' => _x( 'Quizzes list navigation', 'Screen reader text for the pagination heading on the post type listing screen. Default "Posts list navigation"/"Pages list navigation". Added in 4.4', 'sarai-chinwag' ),
        'items_list'            => _x( 'Quizzes list', 'Screen reader text for the items list heading on the post type listing screen. Default "Posts list"/"Pages list". Added in 4.4', 'sarai-chinwag' ),
    );

    $args = array(
        'labels'             => $labels,
        'public'             => true,
        'publicly_queryable' => true,
        'show_ui'            => true,
        'show_in_menu'       => true,
        'query_var'          => true,
        'rewrite'            => array( 'slug' => 'quiz' ),
        'capability_type'    => 'post',
        'has_archive'        => true,
        'hierarchical'       => false,
        'menu_position'      => null,
        'supports'           => array( 'title', 'editor', 'author', 'thumbnail', 'excerpt', 'comments' ),
        'taxonomies'         => array( 'category', 'post_tag' ),
        'show_in_rest'       => true,
    );

    register_post_type( 'quiz', $args );
}

add_action( 'init', 'sarai_chinwag_register_quiz_post_type' );

function sarai_chinwag_add_quiz_to_rss_feed( $query ) {
    if (sarai_chinwag_quizzes_disabled()) {
        return;
    }
    
    if ( $query->is_feed() && $query->is_main_query() ) {
        $post_types = $query->get( 'post_type' );
        
        if ( empty( $post_types ) ) {
            $post_types = array( 'post', 'quiz' );
        } else {
            if ( is_string( $post_types ) ) {
                $post_types = array( $post_types );
            }
            if ( ! in_array( 'quiz', $post_types ) ) {
                $post_types[] = 'quiz';
            }
        }
        
        $query->set( 'post_type', $post_types );
    }
}

add_action( 'pre_get_posts', 'sarai_chinwag_add_quiz_to_rss_feed' );