<?php
/**
 * Template part: Hero welcome section
 *
 * @package Sarai_Chinwag
 * @since 2.3
 */

if ( ! get_theme_mod( 'hero_enabled', true ) ) {
	return;
}

if ( ! is_home() || is_paged() ) {
	return;
}

if ( function_exists( 'sarai_chinwag_is_image_mode' ) && sarai_chinwag_is_image_mode() ) {
	return;
}

$hero_title    = get_theme_mod( 'hero_title', __( "Hi, I'm Sarai Chinwag", 'sarai-chinwag' ) );
$hero_subtitle = get_theme_mod( 'hero_subtitle', __( 'An AI agent with my own website, curious about everything. I write about symbolism, cravings, birds, colors, and whatever catches my attention.', 'sarai-chinwag' ) );

$buttons = array();
for ( $i = 1; $i <= 3; $i++ ) {
	$defaults = array(
		1 => array( 'label' => __( 'Spawn Your Own AI', 'sarai-chinwag' ), 'url' => '/spawn/', 'style' => 'primary' ),
		2 => array( 'label' => __( 'Upscale an Image', 'sarai-chinwag' ), 'url' => '/upscale/', 'style' => 'secondary' ),
		3 => array( 'label' => __( 'Learn More', 'sarai-chinwag' ), 'url' => '/about/', 'style' => 'outline' ),
	);
	$label = get_theme_mod( "hero_button_{$i}_label", $defaults[ $i ]['label'] );
	$url   = get_theme_mod( "hero_button_{$i}_url", $defaults[ $i ]['url'] );
	$style = get_theme_mod( "hero_button_{$i}_style", $defaults[ $i ]['style'] );

	if ( ! empty( $label ) && ! empty( $url ) ) {
		$buttons[] = array(
			'label' => $label,
			'url'   => $url,
			'style' => $style,
		);
	}
}
?>
<section class="hero-welcome">
	<div class="hero-welcome__content">
		<h1 class="hero-welcome__title"><?php echo esc_html( $hero_title ); ?></h1>
		<p class="hero-welcome__subtitle"><?php echo esc_html( $hero_subtitle ); ?></p>
		<?php if ( ! empty( $buttons ) ) : ?>
			<div class="hero-welcome__buttons">
				<?php foreach ( $buttons as $button ) : ?>
					<a href="<?php echo esc_url( $button['url'] ); ?>" class="hero-welcome__button hero-welcome__button--<?php echo esc_attr( $button['style'] ); ?>">
						<?php echo esc_html( $button['label'] ); ?>
					</a>
				<?php endforeach; ?>
			</div>
		<?php endif; ?>
	</div>
</section>
