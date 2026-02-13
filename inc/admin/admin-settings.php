<?php
/**
 * Admin Settings Page
 *
 * Theme settings interface for API keys and functionality toggles
 *
 * @package Sarai_Chinwag
 * @since 2.0
 */

function sarai_chinwag_add_admin_menu() {
    add_options_page(
        __('Theme Settings', 'sarai-chinwag'),
        __('Theme Settings', 'sarai-chinwag'),
        'manage_options',
        'sarai-chinwag-settings',
        'sarai_chinwag_settings_page'
    );
}
add_action('admin_menu', 'sarai_chinwag_add_admin_menu');

function sarai_chinwag_settings_init() {
    register_setting('sarai_chinwag_settings', 'sarai_chinwag_indexnow_key');
    register_setting('sarai_chinwag_settings', 'sarai_chinwag_disable_recipes');
    register_setting('sarai_chinwag_settings', 'sarai_chinwag_disable_quizzes');
    register_setting('sarai_chinwag_settings', 'sarai_chinwag_google_fonts_api_key');
    register_setting('sarai_chinwag_settings', 'sarai_chinwag_pinterest_username');
    register_setting('sarai_chinwag_settings', 'sarai_chinwag_turnstile_site_key');
    register_setting('sarai_chinwag_settings', 'sarai_chinwag_turnstile_secret_key');

    add_settings_section(
        'sarai_chinwag_settings_section',
        __('API Configuration', 'sarai-chinwag'),
        'sarai_chinwag_settings_section_callback',
        'sarai_chinwag_settings'
    );

    add_settings_section(
        'sarai_chinwag_contact_section',
        __('Contact Form Settings', 'sarai-chinwag'),
        'sarai_chinwag_contact_section_callback',
        'sarai_chinwag_settings'
    );

    add_settings_section(
        'sarai_chinwag_functionality_section',
        __('Theme Functionality', 'sarai-chinwag'),
        'sarai_chinwag_functionality_section_callback',
        'sarai_chinwag_settings'
    );
    
    add_settings_field(
        'sarai_chinwag_indexnow_key',
        __('IndexNow API Key', 'sarai-chinwag'),
        'sarai_chinwag_indexnow_key_render',
        'sarai_chinwag_settings',
        'sarai_chinwag_settings_section'
    );
    
    add_settings_field(
        'sarai_chinwag_google_fonts_api_key',
        __('Google Fonts API Key', 'sarai-chinwag'),
        'sarai_chinwag_google_fonts_api_key_render',
        'sarai_chinwag_settings',
        'sarai_chinwag_settings_section'
    );
    
    add_settings_field(
        'sarai_chinwag_pinterest_username',
        __('Pinterest Username', 'sarai-chinwag'),
        'sarai_chinwag_pinterest_username_render',
        'sarai_chinwag_settings',
        'sarai_chinwag_settings_section'
    );
    
    add_settings_field(
        'sarai_chinwag_turnstile_site_key',
        __('Cloudflare Turnstile Site Key', 'sarai-chinwag'),
        'sarai_chinwag_turnstile_site_key_render',
        'sarai_chinwag_settings',
        'sarai_chinwag_contact_section'
    );

    add_settings_field(
        'sarai_chinwag_turnstile_secret_key',
        __('Cloudflare Turnstile Secret Key', 'sarai-chinwag'),
        'sarai_chinwag_turnstile_secret_key_render',
        'sarai_chinwag_settings',
        'sarai_chinwag_contact_section'
    );

    add_settings_field(
        'sarai_chinwag_disable_recipes',
        __('Disable Recipe Functionality', 'sarai-chinwag'),
        'sarai_chinwag_disable_recipes_render',
        'sarai_chinwag_settings',
        'sarai_chinwag_functionality_section'
    );

    add_settings_field(
        'sarai_chinwag_disable_quizzes',
        __('Disable Quiz Functionality', 'sarai-chinwag'),
        'sarai_chinwag_disable_quizzes_render',
        'sarai_chinwag_settings',
        'sarai_chinwag_functionality_section'
    );
}
add_action('admin_init', 'sarai_chinwag_settings_init');

function sarai_chinwag_settings_section_callback() {
    echo '<p>' . __('Configure API keys and external service settings for your theme.', 'sarai-chinwag') . '</p>';
}

function sarai_chinwag_contact_section_callback() {
    echo '<p>' . __('Configure Cloudflare Turnstile and contact form settings. The form is embedded using the shortcode: <code>[sarai_contact_form]</code>', 'sarai-chinwag') . '</p>';
}

function sarai_chinwag_functionality_section_callback() {
    echo '<p>' . __('Control which theme features are enabled or disabled.', 'sarai-chinwag') . '</p>';
}

function sarai_chinwag_indexnow_key_render() {
    $value = get_option('sarai_chinwag_indexnow_key', '');
    echo '<input type="text" name="sarai_chinwag_indexnow_key" value="' . esc_attr($value) . '" size="50" />';
    echo '<p class="description">' . __('Enter your IndexNow API key for automatic search engine indexing. Leave empty to disable IndexNow functionality.', 'sarai-chinwag') . '</p>';
    echo '<p class="description">' . __('Format: 32-character hexadecimal string (e.g., 4ee5f0302df14ea9b2d2f5e9dd919fb0)', 'sarai-chinwag') . '</p>';
}

function sarai_chinwag_google_fonts_api_key_render() {
    $value = get_option('sarai_chinwag_google_fonts_api_key', '');
    echo '<input type="text" name="sarai_chinwag_google_fonts_api_key" value="' . esc_attr($value) . '" size="50" />';
    echo '<p class="description">' . __('Enter your Google Fonts API key to enable dynamic font loading. Required for accessing all Google Fonts.', 'sarai-chinwag') . '</p>';
    echo '<p class="description">' . __('Get your API key at: <a href="https://developers.google.com/fonts/docs/developer_api" target="_blank">Google Fonts Developer API</a>', 'sarai-chinwag') . '</p>';
}

function sarai_chinwag_pinterest_username_render() {
    $value = get_option('sarai_chinwag_pinterest_username', '');
    echo '<input type="text" name="sarai_chinwag_pinterest_username" value="' . esc_attr($value) . '" size="30" />';
    echo '<p class="description">' . __('Enter your Pinterest username to display a Pinterest follow widget in the sidebar. Leave empty to disable Pinterest integration.', 'sarai-chinwag') . '</p>';
    echo '<p class="description">' . __('Example: If your Pinterest URL is pinterest.com/yourname, enter "yourname"', 'sarai-chinwag') . '</p>';
}

function sarai_chinwag_turnstile_site_key_render() {
    $value = get_option('sarai_chinwag_turnstile_site_key', '');
    echo '<input type="text" name="sarai_chinwag_turnstile_site_key" value="' . esc_attr($value) . '" size="50" />';
    echo '<p class="description">' . __('Enter your Cloudflare Turnstile site key (public key). Required for contact form bot protection.', 'sarai-chinwag') . '</p>';
    echo '<p class="description">' . __('Get your keys at: <a href="https://dash.cloudflare.com/?to=/:account/turnstile" target="_blank">Cloudflare Turnstile Dashboard</a>', 'sarai-chinwag') . '</p>';
}

function sarai_chinwag_turnstile_secret_key_render() {
    $value = get_option('sarai_chinwag_turnstile_secret_key', '');
    echo '<input type="password" name="sarai_chinwag_turnstile_secret_key" value="' . esc_attr($value) . '" size="50" />';
    echo '<p class="description">' . __('Enter your Cloudflare Turnstile secret key (private key). This is used for server-side verification.', 'sarai-chinwag') . '</p>';
}

function sarai_chinwag_disable_recipes_render() {
    $value = get_option('sarai_chinwag_disable_recipes', false);
    echo '<input type="checkbox" name="sarai_chinwag_disable_recipes" value="1" ' . checked(1, $value, false) . ' />';
    echo '<p class="description">' . __('When enabled, completely disables all recipe-related functionality. Existing recipe posts remain accessible via direct URL but are not discoverable.', 'sarai-chinwag') . '</p>';
}

function sarai_chinwag_disable_quizzes_render() {
    $value = get_option('sarai_chinwag_disable_quizzes', false);
    echo '<input type="checkbox" name="sarai_chinwag_disable_quizzes" value="1" ' . checked(1, $value, false) . ' />';
    echo '<p class="description">' . __('When enabled, completely disables all quiz-related functionality. Existing quiz posts remain accessible via direct URL but are not discoverable.', 'sarai-chinwag') . '</p>';
}

function sarai_chinwag_settings_page() {
    ?>
    <div class="wrap">
        <h1><?php echo esc_html__('Theme Settings', 'sarai-chinwag'); ?></h1>
        
        <?php
        // Display admin notices
        if (isset($_GET['settings-updated']) && $_GET['settings-updated']) {
            add_settings_error('sarai_chinwag_messages', 'sarai_chinwag_message', __('Settings saved successfully.', 'sarai-chinwag'), 'updated');
        }
        settings_errors('sarai_chinwag_messages');
        ?>
        
        <form action="options.php" method="post">
            <?php
            settings_fields('sarai_chinwag_settings');
            do_settings_sections('sarai_chinwag_settings');
            submit_button(__('Save Settings', 'sarai-chinwag'));
            ?>
        </form>
    </div>
    <?php
}

function sarai_chinwag_sanitize_indexnow_key($input) {
    $input = sanitize_text_field($input);
    
    if (!empty($input) && !preg_match('/^[a-f0-9]{32}$/', $input)) {
        add_settings_error(
            'sarai_chinwag_indexnow_key',
            'invalid-key',
            __('IndexNow API key must be a 32-character hexadecimal string.', 'sarai-chinwag')
        );
        return get_option('sarai_chinwag_indexnow_key', '');
    }
    
    return $input;
}
add_filter('pre_update_option_sarai_chinwag_indexnow_key', 'sarai_chinwag_sanitize_indexnow_key');

function sarai_chinwag_turnstile_admin_notice() {
    $screen = get_current_screen();

    if (!$screen || strpos($screen->id, 'sarai-chinwag') === false) {
        return;
    }

    $site_key = get_option('sarai_chinwag_turnstile_site_key', '');
    $secret_key = get_option('sarai_chinwag_turnstile_secret_key', '');

    if (empty($site_key) || empty($secret_key)) {
        ?>
        <div class="notice notice-warning">
            <p>
                <strong><?php _e('Cloudflare Turnstile Not Configured', 'sarai-chinwag'); ?></strong><br>
                <?php _e('The contact form requires Cloudflare Turnstile keys to function. Please configure your Turnstile site key and secret key in ', 'sarai-chinwag'); ?>
                <a href="<?php echo admin_url('options-general.php?page=sarai-chinwag-settings'); ?>"><?php _e('Theme Settings', 'sarai-chinwag'); ?></a>.
            </p>
        </div>
        <?php
    }
}
add_action('admin_notices', 'sarai_chinwag_turnstile_admin_notice');
?>