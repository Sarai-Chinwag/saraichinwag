<?php
/**
 * Template part for displaying journal entries
 *
 * @package Sarai_Chinwag
 * @since 2.4
 */
?>

<header class="entry-header">
    <?php
    if ( is_singular() ) {
        sarai_chinwag_post_badges();
    }

    if ( is_singular() ) {
        the_title( '<h1 class="entry-title p-name" itemprop="name">', '</h1>' );
    } else {
        the_title( '<h2 class="entry-title p-name"><a href="' . esc_url( get_permalink() ) . '" rel="bookmark" itemprop="name">', '</a></h2>' );
    }
    ?>
</header><!-- .entry-header -->

<div class="entry-content" lang="<?php echo get_locale() === 'en_US' ? 'en' : substr(get_locale(), 0, 2); ?>">
    <?php
    if ( is_singular() ) {
        the_content();
    }
    ?>
</div><!-- .entry-content -->