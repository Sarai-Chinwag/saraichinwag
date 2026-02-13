<?php
function sarai_chinwag_redirect_to_random_post() {
    if (is_page('random-post')) {
        // Use cached random post ID for high performance (replaces expensive orderby => 'rand')
        $random_post_id = sarai_chinwag_get_cached_random_post_id('post');
        
        if ($random_post_id) {
            $permalink = get_permalink($random_post_id);
            if ($permalink) {
                wp_redirect($permalink);
                exit;
            }
        }
        
        // Fallback to homepage if no random post found
        wp_redirect(home_url());
        exit;
    }
}
add_action('template_redirect', 'sarai_chinwag_redirect_to_random_post');

function sarai_chinwag_redirect_to_random_recipe() {
    // Skip if recipes are disabled
    if (sarai_chinwag_recipes_disabled()) {
        // Redirect to random post instead
        wp_redirect(home_url('/random-post'));
        exit;
    }
    
    if (is_page('random-recipe')) {
        // Use cached random recipe ID for high performance
        $random_recipe_id = sarai_chinwag_get_cached_random_post_id('recipe');
        
        if ($random_recipe_id) {
            $permalink = get_permalink($random_recipe_id);
            if ($permalink) {
                wp_redirect($permalink);
                exit;
            }
        }
        
        // Fallback to random post if no recipe found
        wp_redirect(home_url('/random-post'));
        exit;
    }
}
add_action('template_redirect', 'sarai_chinwag_redirect_to_random_recipe');

function sarai_chinwag_redirect_to_random_quiz() {
    // Skip if quizzes are disabled
    if (sarai_chinwag_quizzes_disabled()) {
        // Redirect to random post instead
        wp_redirect(home_url('/random-post'));
        exit;
    }
    
    if (is_page('random-quiz')) {
        // Use cached random quiz ID for high performance
        $random_quiz_id = sarai_chinwag_get_cached_random_post_id('quiz');
        
        if ($random_quiz_id) {
            $permalink = get_permalink($random_quiz_id);
            if ($permalink) {
                wp_redirect($permalink);
                exit;
            }
        }
        
        // Fallback to random post if no quiz found
        wp_redirect(home_url('/random-post'));
        exit;
    }
}
add_action('template_redirect', 'sarai_chinwag_redirect_to_random_quiz');

function sarai_chinwag_redirect_to_random_all() {
    if (is_page('random-all')) {
        // Build array of possible post types
        $post_types = array('post');
        if (!sarai_chinwag_recipes_disabled()) {
            $post_types[] = 'recipe';
        }
        if (!sarai_chinwag_quizzes_disabled()) {
            $post_types[] = 'quiz';
        }
        
        // Randomly select a post type first, then get random post from that type
        $random_post_type = $post_types[array_rand($post_types)];
        $random_post_id = sarai_chinwag_get_cached_random_post_id($random_post_type);
        
        if ($random_post_id) {
            $permalink = get_permalink($random_post_id);
            if ($permalink) {
                wp_redirect($permalink);
                exit;
            }
        }
        
        // Fallback to homepage if no random post found
        wp_redirect(home_url());
        exit;
    }
}
add_action('template_redirect', 'sarai_chinwag_redirect_to_random_all');

