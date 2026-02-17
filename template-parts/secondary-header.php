<?php
/**
 * Template part: Secondary header navigation
 *
 * @package Sarai_Chinwag
 * @since 2.3
 */

if ( ! get_theme_mod( 'secondary_nav_enabled', false ) ) {
	return;
}

if ( ! has_nav_menu( 'secondary' ) ) {
	return;
}
?>
<nav class="secondary-header" role="navigation" aria-label="<?php esc_attr_e( 'Secondary Navigation', 'sarai-chinwag' ); ?>">
	<?php
	wp_nav_menu( array(
		'theme_location' => 'secondary',
		'container'      => false,
		'items_wrap'     => '%3$s',
		'depth'          => 1,
		'fallback_cb'    => false,
	) );
	?>
</nav>
