<?php
/**
 * Image Mode Abilities API Registration
 *
 * Registers WordPress Abilities API endpoints for querying posts and images,
 * replacing the legacy wp_ajax handlers with structured, discoverable abilities.
 *
 * @package Sarai_Chinwag
 * @since 2.3.0
 */

/**
 * Register the Sarai Chinwag ability category.
 *
 * @since 2.3.0
 */
function sarai_chinwag_register_ability_category() {
	if ( ! function_exists( 'wp_register_ability_category' ) ) {
		return;
	}

	wp_register_ability_category(
		'sarai-chinwag',
		array(
			'label'       => __( 'Sarai Chinwag Theme', 'sarai-chinwag' ),
			'description' => __( 'Theme abilities for content querying and image galleries.', 'sarai-chinwag' ),
		)
	);
}

if ( did_action( 'wp_abilities_api_categories_init' ) ) {
	sarai_chinwag_register_ability_category();
} else {
	add_action( 'wp_abilities_api_categories_init', 'sarai_chinwag_register_ability_category' );
}

/**
 * Register image mode abilities.
 *
 * @since 2.3.0
 */
function sarai_chinwag_register_image_mode_abilities() {
	if ( ! function_exists( 'wp_register_ability' ) ) {
		return;
	}

	$sort_enum = array( 'random', 'popular', 'recent', 'oldest' );
	$type_enum = array( 'all', 'posts', 'recipes', 'quizzes' );

	$common_input = array(
		'type'       => 'object',
		'properties' => array(
			'sort_by'          => array(
				'type'    => 'string',
				'enum'    => $sort_enum,
				'default' => 'random',
			),
			'post_type_filter' => array(
				'type'    => 'string',
				'enum'    => $type_enum,
				'default' => 'all',
			),
			'loaded_ids'       => array(
				'type'    => 'array',
				'items'   => array( 'type' => 'integer' ),
				'default' => array(),
			),
			'category'         => array(
				'type'    => 'string',
				'default' => '',
			),
			'tag'              => array(
				'type'    => 'string',
				'default' => '',
			),
			'search'           => array(
				'type'    => 'string',
				'default' => '',
			),
		),
	);

	// query-posts ability.
	wp_register_ability(
		'sarai-chinwag/query-posts',
		array(
			'label'               => __( 'Query Posts', 'sarai-chinwag' ),
			'description'         => __( 'Query and filter posts with sorting, type filtering, and context awareness.', 'sarai-chinwag' ),
			'category'            => 'sarai-chinwag',
			'execute_callback'    => 'sarai_chinwag_ability_query_posts',
			'input_schema'        => $common_input,
			'output_schema'       => array(
				'type'       => 'object',
				'properties' => array(
					'html'  => array( 'type' => 'string' ),
					'count' => array( 'type' => 'integer' ),
				),
			),
			'permission_callback' => '__return_true',
			'meta'                => array( 'show_in_rest' => true ),
		)
	);

	// query-images ability (adds all_site input).
	$image_input               = $common_input;
	$image_input['properties']['all_site'] = array(
		'type'    => 'boolean',
		'default' => false,
	);

	wp_register_ability(
		'sarai-chinwag/query-images',
		array(
			'label'               => __( 'Query Images', 'sarai-chinwag' ),
			'description'         => __( 'Query and filter images for gallery mode with sorting, type filtering, and context awareness.', 'sarai-chinwag' ),
			'category'            => 'sarai-chinwag',
			'execute_callback'    => 'sarai_chinwag_ability_query_images',
			'input_schema'        => $image_input,
			'output_schema'       => array(
				'type'       => 'object',
				'properties' => array(
					'html'  => array( 'type' => 'string' ),
					'count' => array( 'type' => 'integer' ),
				),
			),
			'permission_callback' => '__return_true',
			'meta'                => array( 'show_in_rest' => true ),
		)
	);
}

if ( did_action( 'wp_abilities_api_init' ) ) {
	sarai_chinwag_register_image_mode_abilities();
} else {
	add_action( 'wp_abilities_api_init', 'sarai_chinwag_register_image_mode_abilities' );
}

/**
 * Resolve post types array from post_type_filter string.
 *
 * @param string $post_type_filter Filter value.
 * @return array Post type slugs.
 * @since 2.3.0
 */
function sarai_chinwag_resolve_post_types( $post_type_filter ) {
	if ( 'posts' === $post_type_filter ) {
		return array( 'post' );
	}

	if ( 'recipes' === $post_type_filter ) {
		return array( 'recipe' );
	}

	if ( 'quizzes' === $post_type_filter ) {
		return array( 'quiz' );
	}

	// "all" — include enabled types.
	$types = array( 'post' );

	if ( ! sarai_chinwag_recipes_disabled() ) {
		$types[] = 'recipe';
	}

	if ( function_exists( 'sarai_chinwag_quizzes_disabled' ) && ! sarai_chinwag_quizzes_disabled() ) {
		$types[] = 'quiz';
	}

	return $types;
}

/**
 * Apply sort parameters to a WP_Query args array.
 *
 * @param array  $args    Query args (modified in place).
 * @param string $sort_by Sort method.
 * @return array Modified args.
 * @since 2.3.0
 */
function sarai_chinwag_apply_sort_args( $args, $sort_by ) {
	switch ( $sort_by ) {
		case 'popular':
			$args['meta_key']   = '_post_views';
			$args['orderby']    = 'meta_value_num date';
			$args['order']      = 'DESC';
			$args['meta_query'] = array(
				array(
					'key'     => '_post_views',
					'compare' => 'EXISTS',
				),
			);
			break;

		case 'recent':
			$args['orderby'] = 'date';
			$args['order']   = 'DESC';
			break;

		case 'oldest':
			$args['orderby'] = 'date';
			$args['order']   = 'ASC';
			break;

		case 'random':
		default:
			$args['orderby'] = 'rand';
			break;
	}

	return $args;
}

/**
 * Ability callback: Query Posts.
 *
 * @param array $input Validated input from schema.
 * @return array Output with 'html' and 'count'.
 * @since 2.3.0
 */
function sarai_chinwag_ability_query_posts( $input ) {
	$sort_by          = isset( $input['sort_by'] ) ? $input['sort_by'] : 'random';
	$post_type_filter = isset( $input['post_type_filter'] ) ? $input['post_type_filter'] : 'all';
	$loaded_ids       = isset( $input['loaded_ids'] ) ? array_map( 'absint', $input['loaded_ids'] ) : array();
	$category         = isset( $input['category'] ) ? sanitize_text_field( $input['category'] ) : '';
	$tag              = isset( $input['tag'] ) ? sanitize_text_field( $input['tag'] ) : '';
	$search           = isset( $input['search'] ) ? sanitize_text_field( $input['search'] ) : '';

	$posts_per_page = get_option( 'posts_per_page', 10 );
	$post_types     = sarai_chinwag_resolve_post_types( $post_type_filter );

	$args = array(
		'post_type'      => $post_types,
		'posts_per_page' => $posts_per_page,
		'post_status'    => 'publish',
		'post__not_in'   => $loaded_ids,
	);

	if ( $category ) {
		$args['category_name'] = $category;
	}
	if ( $tag ) {
		$args['tag'] = $tag;
	}
	if ( $search ) {
		$args['s'] = $search;
	}

	$args = sarai_chinwag_apply_sort_args( $args, $sort_by );

	$query = new WP_Query( $args );
	$count = 0;

	ob_start();
	if ( $query->have_posts() ) {
		while ( $query->have_posts() ) {
			$query->the_post();
			$count++;
			echo '<article id="post-' . get_the_ID() . '" class="' . esc_attr( implode( ' ', get_post_class() ) ) . '">';
			get_template_part( 'template-parts/content', get_post_type() );
			echo '</article>';
		}
		wp_reset_postdata();
	}
	$html = ob_get_clean();

	return array(
		'html'  => $html,
		'count' => $count,
	);
}

/**
 * Ability callback: Query Images.
 *
 * @param array $input Validated input from schema.
 * @return array Output with 'html' and 'count'.
 * @since 2.3.0
 */
function sarai_chinwag_ability_query_images( $input ) {
	$sort_by          = isset( $input['sort_by'] ) ? $input['sort_by'] : 'random';
	$post_type_filter = isset( $input['post_type_filter'] ) ? $input['post_type_filter'] : 'all';
	$loaded_ids       = isset( $input['loaded_ids'] ) ? array_map( 'absint', $input['loaded_ids'] ) : array();
	$category         = isset( $input['category'] ) ? sanitize_text_field( $input['category'] ) : '';
	$tag              = isset( $input['tag'] ) ? sanitize_text_field( $input['tag'] ) : '';
	$search           = isset( $input['search'] ) ? sanitize_text_field( $input['search'] ) : '';
	$is_all_site      = ! empty( $input['all_site'] );

	$posts_per_page = get_option( 'posts_per_page', 10 );

	if ( $is_all_site ) {
		$images = sarai_chinwag_get_filtered_all_site_images( $sort_by, $post_type_filter, $loaded_ids, $posts_per_page );
	} elseif ( $search ) {
		$images = sarai_chinwag_get_filtered_search_images( $search, $sort_by, $post_type_filter, $loaded_ids, $posts_per_page );
	} else {
		$term      = null;
		$term_type = '';

		if ( $category ) {
			$term      = get_term_by( 'slug', $category, 'category' );
			$term_type = 'category';
		} elseif ( $tag ) {
			$term      = get_term_by( 'slug', $tag, 'post_tag' );
			$term_type = 'post_tag';
		}

		if ( ! $term ) {
			return array(
				'html'  => '<p>' . esc_html__( 'No images found.', 'sarai-chinwag' ) . '</p>',
				'count' => 0,
			);
		}

		$images = sarai_chinwag_get_filtered_term_images( $term->term_id, $term_type, $sort_by, $post_type_filter, $loaded_ids, $posts_per_page );
	}

	$count = count( $images );

	ob_start();
	if ( empty( $images ) ) {
		echo '<p>' . esc_html__( 'No more images found.', 'sarai-chinwag' ) . '</p>';
	} else {
		foreach ( $images as $index => $image ) {
			include get_template_directory() . '/template-parts/gallery-item.php';
		}
	}
	$html = ob_get_clean();

	return array(
		'html'  => $html,
		'count' => $count,
	);
}
