<?php
/**
 * Hero section and secondary navigation hooks
 *
 * @package Sarai_Chinwag
 * @since 2.3
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Render the secondary header navigation.
 */
add_action( 'after_header', function () {
	get_template_part( 'template-parts/secondary-header' );
} );

/**
 * Render the hero welcome section above the post grid.
 */
add_action( 'before_post_grid', function () {
	get_template_part( 'template-parts/hero-welcome' );
} );
