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
        echo '<nav class="archive-breadcrumbs" aria-label="' . esc_attr__( 'Journal navigation', 'sarai-chinwag' ) . '">';
        echo '<a href="' . esc_url( home_url( '/' ) ) . '">' . esc_html__( 'Home', 'sarai-chinwag' ) . '</a> &gt; ';
        echo '<a href="' . esc_url( get_post_type_archive_link( 'journal' ) ) . '">' . esc_html__( 'Journal', 'sarai-chinwag' ) . '</a> &gt; ';
        echo esc_html( get_the_title() );
        echo '</nav>';
    }

    if ( is_singular() ) {
        the_title( '<h1 class="entry-title p-name" itemprop="name">', '</h1>' );
        echo '<time class="journal-date" datetime="' . esc_attr( get_the_date( 'c' ) ) . '">' . esc_html( get_the_date() ) . '</time>';
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