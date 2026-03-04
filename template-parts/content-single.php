<?php
/**
 * Template part for displaying single posts and recipes
 *
 * @package Sarai_Chinwag
 */
?>

<header class="entry-header">
    <?php
    sarai_chinwag_page_breadcrumbs();
    sarai_chinwag_post_badges();
    
    the_title( '<h1 class="entry-title">', '</h1>' );
    
    ?>
</header><!-- .entry-header -->

<div class="entry-content">
    <?php
    sarai_chinwag_display_featured_image_as_block('large', array(
        'fetchpriority' => 'high',
        'decoding'      => 'async',
        'loading'       => 'eager',
    ));

    the_content();

    wp_link_pages( array(
        'before' => '<div class="page-links">' . esc_html__( 'Pages:', 'sarai-chinwag' ),
        'after'  => '</div>',
    ) );
    ?>
</div><!-- .entry-content -->
