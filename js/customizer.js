/**
 * Customizer live preview functionality
 *
 * @package Sarai_Chinwag
 */

(function($) {
    'use strict';

    // Primary Color
    wp.customize('sarai_chinwag_primary_color', function(value) {
        value.bind(function(newval) {
            document.documentElement.style.setProperty('--color-primary', newval);
        });
    });

    // Secondary Color
    wp.customize('sarai_chinwag_secondary_color', function(value) {
        value.bind(function(newval) {
            document.documentElement.style.setProperty('--color-secondary', newval);
        });
    });

    // Text Color
    wp.customize('sarai_chinwag_text_color', function(value) {
        value.bind(function(newval) {
            document.documentElement.style.setProperty('--color-text', newval);
        });
    });

    // Background Color
    wp.customize('sarai_chinwag_background_color', function(value) {
        value.bind(function(newval) {
            document.documentElement.style.setProperty('--color-background', newval);
        });
    });

    // Heading Font
    wp.customize('sarai_chinwag_heading_font', function(value) {
        value.bind(function(newval) {
            var fontFamily = getFontFamily(newval);
            document.documentElement.style.setProperty('--font-heading', fontFamily);
            
            // Load Google Font if needed
            if (newval !== 'Gluten' && newval !== 'System Fonts') {
                loadGoogleFont(newval);
            }
        });
    });

    // Site Title Font
    wp.customize('sarai_chinwag_site_title_font', function(value) {
        value.bind(function(newval) {
            var fontFamily = getFontFamily(newval);
            document.documentElement.style.setProperty('--font-site-title', fontFamily);

            // Load Google Font if needed
            if (newval !== 'Gluten' && newval !== 'System Fonts') {
                loadGoogleFont(newval);
            }
        });
    });

    // Body Font
    wp.customize('sarai_chinwag_body_font', function(value) {
        value.bind(function(newval) {
            var fontFamily = getFontFamily(newval);
            document.documentElement.style.setProperty('--font-body', fontFamily);
            
            // Load Google Font if needed
            if (newval !== 'Gluten' && newval !== 'System Fonts') {
                loadGoogleFont(newval);
            }
        });
    });

    // Heading Font Size
    wp.customize('sarai_chinwag_heading_font_size', function(value) {
        value.bind(function(newval) {
            var scale = newval / 50; // Convert percentage to scale (50% = 1.0)
            document.documentElement.style.setProperty('--font-heading-scale', scale);
        });
    });

    // Body Font Size
    wp.customize('sarai_chinwag_body_font_size', function(value) {
        value.bind(function(newval) {
            var scale = newval / 50; // Convert percentage to scale (50% = 1.0)
            document.documentElement.style.setProperty('--font-body-scale', scale);
        });
    });


    /**
     * Get CSS font family value
     */
    function getFontFamily(fontName) {
        switch (fontName) {
            case 'Gluten':
                return "'Gluten', 'Helvetica', Arial, sans-serif";
            case 'System Fonts':
                return "'Helvetica', Arial, sans-serif";
            default:
                return "'" + fontName + "', 'Helvetica', Arial, sans-serif";
        }
    }

    /**
     * Dynamically load Google Font for preview
     */
    function loadGoogleFont(fontName) {
        // Remove existing Google Font links to avoid conflicts
        $('link[href*="fonts.googleapis.com"]').remove();
        
        // Create new Google Font URL
        var fontUrl = 'https://fonts.googleapis.com/css2?family=' + encodeURIComponent(fontName) + ':wght@400;500;600;700&display=swap';
        
        // Add new Google Font link
        $('<link>')
            .attr('rel', 'stylesheet')
            .attr('href', fontUrl)
            .appendTo('head');
    }

})(jQuery);
