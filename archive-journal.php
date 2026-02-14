<?php
/**
 * Journal Archive Template
 *
 * Displays journal entries in a chronological list format
 * rather than the card grid used for posts/recipes/quizzes.
 *
 * @package Sarai_Chinwag
 * @since 2.5
 */

get_header();
?>

<main id="primary" class="site-main journal-archive">
    <?php if ( have_posts() ) : ?>

        <header class="page-header">
            <?php
            sarai_chinwag_archive_breadcrumbs();
            the_archive_title( '<h1 class="page-title">', '</h1>' );
            the_archive_description( '<div class="archive-description">', '</div>' );
            ?>
        </header><!-- .page-header -->

        <div class="journal-list">
            <?php
            while ( have_posts() ) :
                the_post();
            ?>
                <article class="journal-list-item">
                    <time class="journal-list-date" datetime="<?php echo esc_attr( get_the_date( 'c' ) ); ?>"><?php echo esc_html( get_the_date() ); ?></time>
                    <h2 class="journal-list-title">
                        <a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
                    </h2>
                    <?php if ( has_excerpt() ) : ?>
                        <p class="journal-list-excerpt"><?php echo esc_html( get_the_excerpt() ); ?></p>
                    <?php endif; ?>
                </article>
            <?php endwhile; ?>
        </div><!-- .journal-list -->

        <?php
        $total_posts = $wp_query->found_posts;
        $posts_per_page = get_option( 'posts_per_page', 10 );

        if ( $total_posts > $posts_per_page ) :
        ?>
        <div class="load-more-container">
            <button id="load-more" data-page="1">
                <?php esc_html_e( 'Load More', 'sarai-chinwag' ); ?>
            </button>
        </div>
        <?php endif; ?>

    <?php else : ?>

        <p><?php esc_html_e( 'No journal entries found.', 'sarai-chinwag' ); ?></p>

    <?php endif; ?>
</main><!-- #primary -->

<?php
get_footer();
