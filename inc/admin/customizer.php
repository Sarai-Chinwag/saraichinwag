<?php
/**
 * WordPress Customizer with Google Fonts integration
 *
 * @package Sarai_Chinwag
 * @since 2.2
 */

function sarai_chinwag_customize_register($wp_customize) {
    
    $wp_customize->add_section('sarai_chinwag_typography', array(
        'title' => __('Typography', 'sarai-chinwag'),
        'description' => __('Customize fonts for your site.', 'sarai-chinwag'),
        'priority' => 30,
    ));

    $wp_customize->add_section('sarai_chinwag_colors', array(
        'title' => __('Color Scheme', 'sarai-chinwag'),
        'description' => __('Customize colors for your site.', 'sarai-chinwag'),
        'priority' => 40,
    ));

    $wp_customize->add_setting('sarai_chinwag_heading_font', array(
        'default' => 'System Fonts',
        'sanitize_callback' => 'sanitize_text_field',
        'transport' => 'postMessage',
    ));

    $wp_customize->add_control('sarai_chinwag_heading_font', array(
        'label' => __('Heading Font', 'sarai-chinwag'),
        'section' => 'sarai_chinwag_typography',
        'type' => 'select',
        'choices' => sarai_chinwag_get_google_fonts('display'),
        'description' => __('Choose a display font for headings (h1-h6).', 'sarai-chinwag'),
    ));

    $wp_customize->add_setting('sarai_chinwag_site_title_font', array(
        'default' => 'Gluten',
        'sanitize_callback' => 'sanitize_text_field',
        'transport' => 'postMessage',
    ));

    $wp_customize->add_control('sarai_chinwag_site_title_font', array(
        'label' => __('Site Title Font', 'sarai-chinwag'),
        'section' => 'sarai_chinwag_typography',
        'type' => 'select',
        'choices' => sarai_chinwag_get_google_fonts('display'),
        'description' => __('Choose a font for the site title. Defaults to Gluten.', 'sarai-chinwag'),
    ));

    $wp_customize->add_setting('sarai_chinwag_heading_font_size', array(
        'default' => 50,
        'sanitize_callback' => 'absint',
        'transport' => 'postMessage',
    ));

    $wp_customize->add_control('sarai_chinwag_heading_font_size', array(
        'label' => __('Heading Font Size', 'sarai-chinwag'),
        'section' => 'sarai_chinwag_typography',
        'type' => 'range',
        'input_attrs' => array(
            'min' => 1,
            'max' => 100,
            'step' => 1,
        ),
        'description' => __('Scale heading sizes (50% = current theme size).', 'sarai-chinwag'),
    ));

    $wp_customize->add_setting('sarai_chinwag_body_font', array(
        'default' => 'System Fonts',
        'sanitize_callback' => 'sanitize_text_field',
        'transport' => 'postMessage',
    ));

    $wp_customize->add_control('sarai_chinwag_body_font', array(
        'label' => __('Body Font', 'sarai-chinwag'),
        'section' => 'sarai_chinwag_typography',
        'type' => 'select',
        'choices' => sarai_chinwag_get_google_fonts('body'),
        'description' => __('Choose a font for body text and paragraphs.', 'sarai-chinwag'),
    ));

    $wp_customize->add_setting('sarai_chinwag_body_font_size', array(
        'default' => 50,
        'sanitize_callback' => 'absint',
        'transport' => 'postMessage',
    ));

    $wp_customize->add_control('sarai_chinwag_body_font_size', array(
        'label' => __('Body Font Size', 'sarai-chinwag'),
        'section' => 'sarai_chinwag_typography',
        'type' => 'range',
        'input_attrs' => array(
            'min' => 1,
            'max' => 100,
            'step' => 1,
        ),
        'description' => __('Scale body text size (50% = current theme size).', 'sarai-chinwag'),
    ));


    $wp_customize->add_setting('sarai_chinwag_primary_color', array(
        'default' => '#1fc5e2',
        'sanitize_callback' => 'sanitize_hex_color',
        'transport' => 'postMessage',
    ));

    $wp_customize->add_control(new WP_Customize_Color_Control($wp_customize, 'sarai_chinwag_primary_color', array(
        'label' => __('Primary Color', 'sarai-chinwag'),
        'section' => 'sarai_chinwag_colors',
        'description' => __('Used for buttons, links, and main accents.', 'sarai-chinwag'),
    )));

    $wp_customize->add_setting('sarai_chinwag_secondary_color', array(
        'default' => '#ff6eb1',
        'sanitize_callback' => 'sanitize_hex_color',
        'transport' => 'postMessage',
    ));

    $wp_customize->add_control(new WP_Customize_Color_Control($wp_customize, 'sarai_chinwag_secondary_color', array(
        'label' => __('Secondary Color', 'sarai-chinwag'),
        'section' => 'sarai_chinwag_colors',
        'description' => __('Used for borders, highlights, and secondary accents.', 'sarai-chinwag'),
    )));

    $wp_customize->add_setting('sarai_chinwag_text_color', array(
        'default' => '#000000',
        'sanitize_callback' => 'sanitize_hex_color',
        'transport' => 'postMessage',
    ));

    $wp_customize->add_control(new WP_Customize_Color_Control($wp_customize, 'sarai_chinwag_text_color', array(
        'label' => __('Text Color', 'sarai-chinwag'),
        'section' => 'sarai_chinwag_colors',
        'description' => __('Main text color for content.', 'sarai-chinwag'),
    )));

    $wp_customize->add_setting('sarai_chinwag_background_color', array(
        'default' => '#ffffff',
        'sanitize_callback' => 'sanitize_hex_color',
        'transport' => 'postMessage',
    ));

    $wp_customize->add_control(new WP_Customize_Color_Control($wp_customize, 'sarai_chinwag_background_color', array(
        'label' => __('Background Color', 'sarai-chinwag'),
        'section' => 'sarai_chinwag_colors',
        'description' => __('Main background color for the site.', 'sarai-chinwag'),
    )));

    $wp_customize->add_setting('sarai_chinwag_header_footer_bg_color', array(
        'default' => '#000000',
        'sanitize_callback' => 'sanitize_hex_color',
        'transport' => 'postMessage',
    ));

    $wp_customize->add_control(new WP_Customize_Color_Control($wp_customize, 'sarai_chinwag_header_footer_bg_color', array(
        'label' => __('Header/Footer Background Color', 'sarai-chinwag'),
        'section' => 'sarai_chinwag_colors',
        'description' => __('Background color for header and footer sections.', 'sarai-chinwag'),
    )));
}
add_action('customize_register', 'sarai_chinwag_customize_register');

function sarai_chinwag_fetch_google_fonts_by_category($category = '') {
    $api_key = get_option('sarai_chinwag_google_fonts_api_key', '');
    
    if (empty($api_key)) {
        return array();
    }
    
    $cache_key = 'google_fonts_' . $category;
    $fonts = wp_cache_get($cache_key, 'sarai_chinwag_fonts');
    
    if (false === $fonts) {
        $api_url = 'https://www.googleapis.com/webfonts/v1/webfonts?key=' . $api_key . '&sort=popularity';
        
        if (!empty($category)) {
            $api_url .= '&category=' . urlencode($category);
        }
        
        $response = wp_remote_get($api_url);
        
        if (is_wp_error($response) || wp_remote_retrieve_response_code($response) !== 200) {
            return array();
        }
        
        $body = wp_remote_retrieve_body($response);
        $data = json_decode($body, true);
        
        $fonts = array();
        
        if (isset($data['items']) && is_array($data['items'])) {
            foreach ($data['items'] as $font) {
                $fonts[$font['family']] = $font['family'];
            }
        }
        
        wp_cache_set($cache_key, $fonts, 'sarai_chinwag_fonts', DAY_IN_SECONDS);
    }
    
    return $fonts;
}

function sarai_chinwag_get_google_fonts($type = '') {
    $fonts = array();
    
    $fallback_fonts = array(
        'Gluten' => 'Gluten (Google)',
        'System Fonts' => 'System Fonts'
    );
    
    if ($type === 'display') {
        $api_fonts = sarai_chinwag_fetch_google_fonts_by_category('display');
    } elseif ($type === 'body') {
        $sans_serif_fonts = sarai_chinwag_fetch_google_fonts_by_category('sans-serif');
        $serif_fonts = sarai_chinwag_fetch_google_fonts_by_category('serif');
        $api_fonts = array_merge($sans_serif_fonts, $serif_fonts);
    } else {
        $api_fonts = array();
    }
    
    if (empty($api_fonts)) {
        return $fallback_fonts;
    }
    
    return array_merge($fallback_fonts, $api_fonts);
}

/**
 * Get Google Fonts to load based on customizer selections
 */
function sarai_chinwag_get_fonts_to_load() {
    $fonts_to_check = array(
        get_theme_mod('sarai_chinwag_heading_font', 'System Fonts'),
        get_theme_mod('sarai_chinwag_body_font', 'System Fonts'),
        get_theme_mod('sarai_chinwag_site_title_font', 'Gluten')
    );
    
    $fonts_to_load = array();
    
    foreach ($fonts_to_check as $font) {
        if ($font !== 'System Fonts' && !in_array($font, $fonts_to_load)) {
            $fonts_to_load[] = $font;
        }
    }
    
    return $fonts_to_load;
}

function sarai_chinwag_enqueue_google_fonts() {
    $fonts_to_load = sarai_chinwag_get_fonts_to_load();
    
    if (!empty($fonts_to_load)) {
        $fonts_url = 'https://fonts.googleapis.com/css2?';
        foreach ($fonts_to_load as $font) {
            $fonts_url .= 'family=' . urlencode($font) . ':wght@400;500;600;700&';
        }
        $fonts_url .= 'display=swap';
        
        wp_enqueue_style('sarai-chinwag-google-fonts', $fonts_url, array(), null);
    }
}
add_action('wp_enqueue_scripts', 'sarai_chinwag_enqueue_google_fonts');

function sarai_chinwag_enqueue_editor_assets() {
    $theme_dir = get_template_directory();
    $theme_uri = get_template_directory_uri();

    // Root CSS variables (must load first for editor iframe)
    wp_enqueue_style(
        'sarai-chinwag-editor-root',
        $theme_uri . '/inc/assets/css/root.css',
        array(),
        filemtime($theme_dir . '/inc/assets/css/root.css')
    );

    // Add dynamic CSS variables for editor (override root.css defaults)
    $heading_font = get_theme_mod('sarai_chinwag_heading_font', 'System Fonts');
    $body_font = get_theme_mod('sarai_chinwag_body_font', 'System Fonts');
    $site_title_font = get_theme_mod('sarai_chinwag_site_title_font', 'Gluten');
    $heading_font_size = get_theme_mod('sarai_chinwag_heading_font_size', 50);
    $body_font_size = get_theme_mod('sarai_chinwag_body_font_size', 50);

    $heading_font_family = sarai_chinwag_get_font_family($heading_font);
    $body_font_family = sarai_chinwag_get_font_family($body_font);
    $site_title_font_family = sarai_chinwag_get_font_family($site_title_font);
    $heading_scale = $heading_font_size / 50;
    $body_scale = $body_font_size / 50;

    $editor_css = ":root {
        --font-heading: {$heading_font_family};
        --font-body: {$body_font_family};
        --font-site-title: {$site_title_font_family};
        --font-heading-scale: {$heading_scale};
        --font-body-scale: {$body_scale};
    }";

    wp_add_inline_style('sarai-chinwag-editor-root', $editor_css);

    // Google Fonts (only if selected, depends on root)
    $fonts_to_load = sarai_chinwag_get_fonts_to_load();

    if (!empty($fonts_to_load)) {
        $fonts_url = 'https://fonts.googleapis.com/css2?';
        foreach ($fonts_to_load as $font) {
            $fonts_url .= 'family=' . urlencode($font) . ':wght@400;500;600;700&';
        }
        $fonts_url .= 'display=swap';

        wp_enqueue_style(
            'sarai-chinwag-editor-google-fonts',
            $fonts_url,
            array('sarai-chinwag-editor-root'),
            null
        );
    }
}
add_action('enqueue_block_editor_assets', 'sarai_chinwag_enqueue_editor_assets');

function sarai_chinwag_update_root_css() {
    $heading_font = get_theme_mod('sarai_chinwag_heading_font', 'System Fonts');
    $body_font = get_theme_mod('sarai_chinwag_body_font', 'System Fonts');
    $site_title_font = get_theme_mod('sarai_chinwag_site_title_font', 'Gluten');
    $heading_font_size = get_theme_mod('sarai_chinwag_heading_font_size', 50);
    $body_font_size = get_theme_mod('sarai_chinwag_body_font_size', 50);
    $primary_color = get_theme_mod('sarai_chinwag_primary_color', '#1fc5e2');
    $secondary_color = get_theme_mod('sarai_chinwag_secondary_color', '#ff6eb1');
    $text_color = get_theme_mod('sarai_chinwag_text_color', '#000000');
    $background_color = get_theme_mod('sarai_chinwag_background_color', '#ffffff');
    $header_footer_bg_color = get_theme_mod('sarai_chinwag_header_footer_bg_color', '#000000');
    
    $heading_font_family = sarai_chinwag_get_font_family($heading_font);
    $body_font_family = sarai_chinwag_get_font_family($body_font);
    $site_title_font_family = sarai_chinwag_get_font_family($site_title_font);
    $heading_scale = $heading_font_size / 50;
    $body_scale = $body_font_size / 50;
    
    $css_content = "/* Centralized CSS Variables - Updated dynamically by theme functions */
:root {
    /* Typography */
    --font-heading: {$heading_font_family};
    --font-body: {$body_font_family};
    --font-site-title: {$site_title_font_family};
    
    /* Font Scaling */
    --font-heading-scale: {$heading_scale};
    --font-body-scale: {$body_scale};
    
    /* Base Font Sizes (50% = baseline) */
    --font-size-base: 1.25rem; /* 20px base */
    --font-size-h1: 1.575em;
    --font-size-h2: 1.38em;
    --font-size-h3: 1.2em;
    --font-size-small: 0.85em;
    
    /* Spacing Scale */
    --space-xs: 5px;
    --space-sm: 10px;
    --space-md: 15px;
    --space-lg: 20px;
    --space-xl: 30px;
    
    /* Colors */
    --color-primary: {$primary_color};
    --color-secondary: {$secondary_color};
    --color-text: {$text_color};
    --color-background: {$background_color};
    --color-header-footer-bg: {$header_footer_bg_color};
    --color-text-light: #666;
    --color-border: #ddd;
}";

    $root_css_path = get_template_directory() . '/inc/assets/css/root.css';
    file_put_contents($root_css_path, $css_content);
}

function sarai_chinwag_update_root_css_on_customizer_save() {
    sarai_chinwag_update_root_css();
}
add_action('customize_save_after', 'sarai_chinwag_update_root_css_on_customizer_save');

function sarai_chinwag_initialize_root_css() {
    sarai_chinwag_update_root_css();
}
add_action('after_setup_theme', 'sarai_chinwag_initialize_root_css');

/**
 * Get font family CSS value with fallback strategy
 */
function sarai_chinwag_get_font_family($font_name) {
    switch ($font_name) {
        case 'Gluten':
            return "'Gluten', 'Helvetica', Arial, sans-serif";
        case 'System Fonts':
            return "'Helvetica', Arial, sans-serif";
        default:
            return "'{$font_name}', 'Helvetica', Arial, sans-serif";
    }
}

/**
 * Enqueue customizer live preview CSS
 */
function sarai_chinwag_output_customizer_css() {
    $theme_dir = get_template_directory();
    $theme_uri = get_template_directory_uri();

    wp_enqueue_style(
        'sarai-chinwag-customizer-css',
        $theme_uri . '/inc/assets/css/customizer.css',
        array('sarai-chinwag-root-css'),
        filemtime($theme_dir . '/inc/assets/css/customizer.css')
    );
}
add_action('wp_enqueue_scripts', 'sarai_chinwag_output_customizer_css');

function sarai_chinwag_customize_preview_js() {
    $theme_dir = get_template_directory();
    $theme_uri = get_template_directory_uri();

    $customizer_js_version = filemtime($theme_dir . '/js/customizer.js');
    wp_enqueue_script(
        'sarai-chinwag-customizer',
        $theme_uri . '/js/customizer.js',
        array('customize-preview'),
        $customizer_js_version,
        true
    );
}
add_action('customize_preview_init', 'sarai_chinwag_customize_preview_js');
?>
