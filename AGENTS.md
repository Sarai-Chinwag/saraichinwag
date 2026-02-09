# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## Development Environment

This is a WordPress theme for "Sarai Chinwag" with a production build system. Files can be directly edited for development, with changes immediately reflected. The theme includes build.sh for creating optimized production packages.

### Admin Settings
The theme includes an admin settings page accessible via **Settings → Theme Settings** in the WordPress admin. This page allows configuration of:
- IndexNow API Key for automatic search engine indexing
- Google Fonts API Key for dynamic font loading from Google Fonts API
- Pinterest Username for social media integration
- Cloudflare Turnstile keys for contact form bot protection
- Contact form recipient email and copy settings
- Recipe Functionality Toggle: Completely disable all recipe-related features for universal theme usage

## Theme Architecture

### Core Structure
- **functions.php**: Main theme setup, loads PHP modules from `/inc` directory
- **inc/core/assets.php**: Centralized asset management with dynamic versioning using `filemtime()` for all CSS and JavaScript files
- **style.css**: Primary stylesheet with theme styles (CSS variables centralized in `inc/assets/css/root.css`)
- **inc/assets/css/root.css**: Centralized CSS custom properties and variables
- **inc/assets/css/editor.css**: WordPress Block Editor and Classic Editor font integration
- **inc/assets/css/customizer.css**: Live preview styling for WordPress Customizer
- **Template hierarchy**: Standard WordPress templates (single.php, archive.php, home.php, search.php, page.php, etc.)
- **page.php**: Standard page template using `template-parts/content-single.php` with sidebar support

### Custom Post Types
- **Recipe post type**: Registered via functions in functions.php with full labels and Schema.org markup support
- **Main query modification**: Includes both 'post' and 'recipe' post types in home, category, tag, and search queries

### PHP Module System
The theme uses organized PHP modules in the `/inc` directory loaded via functions.php:
- **inc/admin/**: Admin interface components (settings, customizer, notices)
- **inc/core/**: Core functionality (assets.php for centralized asset management)
- **inc/contact/**: Contact form system (AJAX processing, email handling, Cloudflare Turnstile integration)
- **inc/queries/**: Query modification and content retrieval systems
- **inc/queries/image-mode/**: Complete image gallery and extraction system
- **Core modules**: recipes.php, ratings.php for recipe functionality
- **Integration modules**: bing-index-now.php, yoast-stuff.php for third-party compatibility

### Centralized Asset Management
- **inc/core/assets.php**: Centralized asset management system controlling all CSS and JavaScript enqueuing with conditional loading, dynamic versioning using `filemtime()`, and proper dependency management

### CSS Structure
- **inc/assets/css/root.css**: Centralized CSS custom properties and variables (loaded first)
- **inc/assets/css/customizer.css**: Live preview styling for WordPress Customizer (admin only)
- **inc/assets/css/editor.css**: WordPress Block Editor and Classic Editor font integration
- **inc/assets/css/recipes.css**: Recipe-specific styles (conditionally loaded when viewing recipes)
- **inc/assets/css/single.css**: Single post/page styles (conditionally loaded)
- **inc/assets/css/archive.css**: Archive-specific styles (conditionally loaded)
- **inc/assets/css/image-mode.css**: Image gallery mode styles (conditionally loaded)
- **inc/assets/css/sidebar.css**: Sidebar-specific styles (conditionally loaded)
- **inc/assets/css/contact.css**: Contact form styles (conditionally loaded when shortcode present)
- **style.css**: Primary stylesheet (depends on root.css)

### JavaScript Structure

**Centralized Assets** (loaded via inc/core/assets.php):
- **inc/assets/js/nav.js**: Header search functionality with position adjustment for admin bar
- **inc/assets/js/gallery-utils.js**: Image gallery navigation, lightbox functionality, and gallery-specific interactions
- **inc/assets/js/pinterest.js**: Pinterest save button integration and enhanced social functionality

**Root-Level Scripts** (specialized functionality):
- **js/customizer.js**: Live preview functionality for font changes and size scaling in WordPress Customizer
- **js/filter-bar.js**: Advanced AJAX filtering system with sort options and post type filtering
- **js/load-more.js**: AJAX Load More functionality that preserves filter state
- **js/rating.js**: AJAX rating system with localStorage persistence, nonce security, dual-state management (user + server average), and automatic default 5-star ratings for new recipes
- **js/contact-form.js**: AJAX-powered contact form with Cloudflare Turnstile integration and validation

### Template Parts
- `template-parts/content-recipe.php`: Recipe display with embedded Schema.org markup
- `template-parts/content.php` & `template-parts/content-single.php`: Standard post templates
- `template-parts/content-image-gallery.php`: Specialized template for image gallery posts
- `template-parts/filter-bar.php`: Advanced filter interface for archives and home page
- `template-parts/gallery-item.php`: Individual gallery item display component
- `template-parts/archive-image-mode-link.php`: "Try Image Mode" link for seamless switching to gallery view with accurate image counts and context-aware functionality
- `template-parts/contact-form.php`: AJAX-powered contact form with Cloudflare Turnstile integration

### Key Features
- **Advanced Filter System**: Full-width filter bar with sort options (Random, Most Popular, Recent, Oldest) and post type filtering
- **Complete Image Gallery System**: Advanced image extraction, gallery archives, and specialized display templates with lightbox functionality
- **Image Search & Discovery**: Comprehensive image search system with category/tag-based filtering and extraction
- **Load More Integration**: AJAX-powered infinite scroll that preserves filter state and enhances user experience
- **Full-Width Layout**: Home and archive pages display 4-column responsive grid (sidebar removed for maximum content visibility)
- **View Counter System**: Simple post meta tracking for popularity sorting (`_post_views` field)
- **Randomized content discovery**: Anti-chronological design with randomization as default behavior
- **Random access pages**: `/random-post`, `/random-recipe`, and `/random-all` pages for serendipitous browsing
- **Dynamic Google Fonts system**: API integration with category filtering (display fonts for headings, sans-serif + serif for body)
- **Percentage-based font scaling**: 1-100% size control with 50% = current theme baseline, maintains heading hierarchy
- **Universal theme design**: Can function as recipe site or standard blog via admin toggle
- **Recipe functionality**: Complete recipe post type with default 5-star ratings, user rating system, embedded Schema.org markup, and specialized templates (when enabled)
- **WordPress Customizer integration**: Live preview for font changes and size scaling with custom CSS properties
- **WordPress Editor Font Integration**: Consistent font experience between Block Editor, Classic Editor, and frontend display
- **Pinterest integration**: Footer follow button + automatic Pinterest save buttons with enhanced social functionality
- **Contact form system**: AJAX-powered contact forms with Cloudflare Turnstile bot protection and email notifications
- **Image anchoring**: Automatic anchorable spans on images for deep linking and navigation
- **Gallery discovery badges**: Intelligent gallery links on single posts with image counts
- **Badge-breadcrumb system**: Category/tag badges on single posts for navigation, traditional breadcrumbs on archives
- **Random discovery section**: 3-post random grid replaces complex related posts logic
- **Dynamic asset versioning**: Uses `filemtime()` for cache busting on CSS and JS files
- **Default 5-Star Rating System**: New recipes automatically receive 5.0 rating with 1 review count for immediate display in popularity sorting and rating widgets
- **Interactive Rating System**: AJAX-powered user ratings with localStorage persistence, nonce security, and dual-state management
- **Schema.org markup**: Embedded structured data implementation for recipes in templates
- **Performance optimizations**: Object caching with wp_cache_* functions, specialized cache groups, and limited query results
- **Security enhancements**: All output properly escaped, input sanitized, secure API key storage
- **AJAX template loading**: Dynamic template part loading with nonce security for enhanced interactivity
- **Content-Language header**: Automatic language header setting for browser translation detection

## Default 5-Star Rating System

### Automatic Rating Assignment
The theme implements an automatic default 5-star rating system for all new recipe posts to ensure immediate visibility in popularity sorting and consistent user experience:

**Core Implementation:**
- **sarai_chinwag_set_default_recipe_rating()** (`inc/ratings.php:94`): Automatically assigns 5.0 rating with 1 review count to new published recipes
- **Triggered by WordPress hooks**: `save_post` and `publish_recipe` actions ensure coverage of all publication methods
- **Smart detection**: Only applies to recipe post type with published status, skips recipes that already have ratings
- **Prevents rating gaps**: Eliminates scenarios where new recipes appear unrated or invisible in popularity-based sorting

**Bulk Application:**
- **sarai_chinwag_apply_default_ratings_to_existing()** (`inc/ratings.php:125`): Retroactively applies default ratings to existing recipes without ratings
- **Meta query filtering**: Uses WordPress meta_query to identify recipes lacking rating_value meta
- **Batch processing**: Handles unlimited recipes efficiently for theme upgrades and migrations

**Technical Details:**
- **Meta storage**: Uses `rating_value` (5.0) and `review_count` (1) post meta fields
- **Integration**: Seamlessly works with existing user rating system - user ratings update the average calculation
- **Universal compatibility**: Respects recipe functionality toggle - completely disabled when recipes are turned off

### Interactive User Rating System
Beyond default ratings, users can submit their own ratings through a sophisticated AJAX-powered interface:

**Dual-State Management:**
- **Client-side**: localStorage persistence provides immediate visual feedback
- **Server-side**: AJAX submissions with nonce security update database permanently
- **Average calculation**: New ratings computed as `(($rating_value * $review_count) + $rating) / ($review_count + 1)`
- **Meta verification**: Server validates successful database updates before confirming to client

**Security & Performance:**
- **Nonce protection**: WordPress nonce verification prevents CSRF attacks
- **Input validation**: Rating range (1-5) and post ID validation on server side
- **Error handling**: Graceful degradation with user-friendly error messages
- **Translation ready**: Full wp.i18n integration for internationalization

## Contact Form System

### Cloudflare Turnstile Integration

**Bot Protection**: Complete Cloudflare Turnstile integration for spam prevention
**Configuration**: Site key and secret key stored securely in WordPress options
**Verification**: Server-side token verification with Cloudflare API
**Fallback**: Graceful degradation when Turnstile is not configured

**Settings**:
- `sarai_chinwag_turnstile_site_key`: Public key for frontend widget
- `sarai_chinwag_turnstile_secret_key`: Private key for server verification
- `sarai_chinwag_contact_recipient_email`: Email address for form submissions
- `sarai_chinwag_contact_send_copy`: Option to send confirmation to submitter

### Contact Form Implementation

**Shortcode**: `[sarai_contact_form]` for embedding contact forms in pages/posts
**AJAX Processing**: Real-time form submission without page reload
**Validation**: Client and server-side validation with user-friendly error messages
**Email Handling**: Automated admin notifications and optional submitter confirmations

**Form Fields**:
- Name (required, max 100 chars)
- Email (required, valid email format, max 100 chars)
- Subject (required, max 200 chars)
- Message (required, max 5000 chars)
- Turnstile verification (required)

**Security Features**:
- WordPress nonce verification for CSRF protection
- Input sanitization with appropriate WordPress functions
- Rate limiting through Turnstile verification
- IP address logging for moderation

### Email System

**Admin Notifications**: Formatted emails sent to configured recipient
**Submitter Copies**: Optional confirmation emails to form submitters
**Email Headers**: Proper From, Reply-To, and Content-Type headers
**Internationalization**: All email content translatable

**Email Content**:
- Submission details (name, email, subject, message)
- Timestamp and IP address for moderation
- Formatted for easy reading and moderation

## Complete Image Gallery System

### Image Gallery Architecture
The theme includes a comprehensive image gallery system for enhanced content discovery and visual browsing:

**Core Components:**
- **Image Extractor** (`inc/queries/image-mode/image-extractor.php`): Extracts all images from posts within specific categories/tags
- **Image Archives** (`inc/queries/image-mode/image-archives.php`): Creates image-focused archive pages showing extracted images
- **Image Search** (`inc/queries/image-mode/search-images.php`): Specialized search functionality for image content
- **Rewrite Rules** (`inc/queries/image-mode/rewrite-rules.php`): Custom URL structures for image archives
- **Gallery Templates** (`template-parts/content-image-gallery.php`, `template-parts/gallery-item.php`): Specialized display templates

**Gallery Features:**
- **Automatic Image Extraction**: Extracts featured images, gallery images, and content images from posts
- **Category/Tag Image Archives**: View all images from specific categories or tags in gallery format
- **Image Search Integration**: Search specifically within image content with gallery display
- **Lightbox Functionality**: Enhanced image viewing with navigation via `inc/assets/js/gallery-utils.js`
- **Performance Caching**: wp_cache_* with `sarai_chinwag_images` cache group for fast image retrieval
- **Responsive Gallery Grid**: Adapts to 4-column grid system with optimized image sizing

**URL Structure:**
- Standard archives: `/category/food/` (traditional post listings)
- Image archives: `/category/food/images/` (gallery view of all images from food category)
- Image search: `/search/query/?images=1` (search results in gallery format)

**Technical Implementation:**
- **sarai_chinwag_extract_images_from_term()**: Main extraction function with caching
- **sarai_chinwag_get_image_search_results()**: Image-specific search queries
- **sarai_chinwag_image_archive_query()**: Modifies main query for image archive pages
- **Performance**: Display queries limited to 30 images, count queries use 99999 for accuracy, cached for 2 hours, optimized queries

### Archive Image Mode Link System
The theme includes an intelligent "Try Image Mode" link system that appears on archives to encourage gallery exploration:

**Context-Aware Display:**
- **Homepage**: Links to site-wide image gallery (`/images/`) showing all site images
- **Category Archives**: Links to category-specific image gallery (`/category/name/images/`)
- **Tag Archives**: Links to tag-specific image gallery (`/tag/name/images/`)
- **Search Results**: Links to image search results (`/search/query/?images=1`)

**Smart Functionality:**
- **Accurate Image Counts**: Uses `sarai_chinwag_get_accurate_term_image_count()` and related functions for precise counts
- **Conditional Display**: Only shows when images are available for the context (minimum 1 image)
- **Gallery Detection**: Automatically hides when already viewing image gallery mode
- **Visual Design**: Matches gallery discovery badge system used on single posts for consistency

**Technical Implementation:**
- Template: `template-parts/archive-image-mode-link.php`
- Functions: `sarai_chinwag_get_site_wide_image_count()`, `sarai_chinwag_get_search_image_count()`
- Performance: Leverages existing image caching system with `sarai_chinwag_images` cache group
- Integration: Seamlessly works with existing rewrite rules and image archive system

### Gallery Discovery Badges System

The theme includes intelligent gallery discovery badges that appear on single posts to encourage exploration of related image galleries.

**Context-Aware Display**: Shows relevant gallery links based on post categories and tags
**Image Count Integration**: Displays accurate image counts for each gallery
**Caching**: Cached per-post with 15-minute expiration using `sarai_chinwag_related` cache group
**Visual Design**: Consistent with gallery discovery theme throughout the site

**Badge Types**:
- **Category Galleries**: Blue badges linking to category-specific image galleries
- **Tag Galleries**: Pink badges linking to tag-specific image galleries
- **Count Display**: Shows number of images available in each gallery
- **Navigation**: Direct links to gallery pages with proper URL structure

**Function**: `sarai_chinwag_gallery_discovery_badges()`
**Display Location**: Below post content on single posts and recipes
**Conditional Logic**: Only displays when galleries contain images
**Accessibility**: Proper ARIA labels and semantic navigation structure

## Image Optimization Strategy

**Custom Image Sizes:**
- **grid-thumb**: 450x450px cropped for post grid display (optimized for 412px mobile viewports)
- **Removed unused WordPress defaults**: thumbnail (150px), medium (300px), medium_large (768px) to reduce storage overhead

**Performance Benefits:**
- Single optimized image size reduces server storage and bandwidth usage
- 450px size provides crisp display on mobile devices while remaining lightweight
- Eliminates WordPress generation of unnecessary image variations
- Improves theme loading speed by serving appropriately sized images

## WordPress Editor Font Integration

### WYSIWYG Font Consistency
The theme provides seamless font integration between WordPress editors and frontend display, ensuring content creators see exactly how their content will appear to visitors.

**Editor Support:**
- **Block Editor (Gutenberg)**: Full font integration via `enqueue_block_editor_assets` hook
- **Classic Editor**: Font loading on post edit pages via `admin_enqueue_scripts` hook
- **Dynamic Font Loading**: Same Google Fonts loaded in editors as on frontend
- **Real-time Scaling**: Editor typography scales with customizer settings using CSS custom properties

**Technical Implementation:**
- `sarai_chinwag_enqueue_editor_fonts()`: Loads Google Fonts for both editors using identical font selection logic as frontend
- `sarai_chinwag_enqueue_editor_css()`: Generates editor-specific CSS with proper WordPress selectors
- **Editor CSS Selectors**: `.editor-styles-wrapper`, `.wp-block-editor`, `.wp-block-*` for Block Editor compatibility
- **Classic Editor Support**: Targets post edit pages specifically (`post.php`, `post-new.php`)
- **Font Scaling**: Editors inherit percentage-based scaling from customizer settings

**User Experience Benefits:**
- Content creators see accurate font representation while editing
- Eliminates guesswork about final post appearance
- Improved editorial workflow with consistent typography
- Reduced need for preview during content creation
- Professional editing environment matching site design

**Performance Considerations:**
- Google Fonts loaded only once per editing session
- Conditional loading based on customizer font selections
- Editor CSS generated dynamically to match current theme settings
- Minimal overhead - only loads fonts actually selected in customizer

## Dynamic Google Fonts Architecture

### Font System Components
- **API Integration**: functions.php handles Google Fonts API calls with `sarai_chinwag_fetch_google_fonts_by_category()`
- **Customizer Controls**: Two font dropdowns (Header/Body) + two size sliders (1-100%) accessible via **Appearance → Customize → Typography**
- **CSS Scaling System**: Uses CSS custom properties (`--font-heading-scale`, `--font-body-scale`) for proportional scaling
- **Live Preview**: `js/customizer.js` provides real-time font and size changes in WordPress Customizer
- **Editor Integration**: Consistent font loading in Block Editor and Classic Editor matching frontend selections
- **Caching Strategy**: Object cache groups with `sarai_chinwag_fonts` group for all font-related data, 24-hour wp_cache for Google Fonts API responses using dynamic cache keys

### Font Organization
- **Header Fonts**: Display category fonts only (`category=display` API parameter)
- **Body Fonts**: Combined sans-serif + serif fonts (`category=sans-serif` + `category=serif`)
- **Fallback Strategy**: Google Font → Gluten (theme font) → System fonts

### Scaling System
- **50% = Baseline**: Current theme sizes (20px body, 1.75em h1, 1.38em h2)  
- **Fluid Responsive Scaling**: Uses `clamp()` functions for optimal display across all devices
- **CSS Implementation**: `calc()` functions with custom properties and clamp() for responsive breakpoints
- **Mobile Optimization**: Specific breakpoints at 768px, 600px, and 480px for enhanced mobile typography
- **Hierarchy Preservation**: All heading levels (h1-h6) scale proportionally while maintaining fluid responsiveness

## Randomization System Architecture

### Anti-Chronological Design
- **Main Query Modification**: functions.php modifies home and archive queries to use `orderby: rand`
- **Post Type Integration**: Includes both 'post' and 'recipe' post types in randomized queries
- **Performance Optimizations**: Cached random ID arrays replace expensive `orderby => 'rand'` queries with rotation system and limited datasets (max 500 posts)

### Random Access Pages  
- **Direct Random Posts**: `/random-post` page redirects to random post via `sarai_chinwag_redirect_to_random_post()`
- **Direct Random Recipes**: `/random-recipe` page redirects to random recipe (respects recipe toggle)
- **Direct Random All**: `/random-all` page redirects to random post or recipe (mixed selection)
- **Fallback Logic**: When recipes disabled, random-recipe redirects to random-post instead
- **Implementation**: Uses `WP_Query` with `orderby: rand` and `posts_per_page: 1`

## Advanced Filter System Architecture

### Filter Bar Components
- **Sort Options**: Random (default) | Most Popular (by view count) | Most Recent | Oldest
- **Content Types**: All | Posts | Recipes (only shown when both post types exist)
- **AJAX Integration**: Real-time filtering without page reload, preserves filter state across Load More
- **Mobile Responsive**: Collapsible interface with touch-friendly buttons (44px minimum height)
- **Load More Integration**: Seamless infinite scroll that respects active filters and maintains user context

### Image Gallery Integration
- **Specialized Template**: `template-parts/content-image-gallery.php` handles gallery post display
- **Gallery Items**: Individual gallery components via `template-parts/gallery-item.php`
- **Filter Compatibility**: Gallery posts integrate seamlessly with the advanced filtering system
- **Responsive Design**: Gallery layout adapts to 4-column grid system with optimized image sizing

### View Counter System
- **Storage**: Simple post meta (`_post_views`) incremented on each post view
- **Tracking**: Automatic on `wp_head` for singular posts/recipes only
- **Performance**: Efficient meta queries for popularity sorting
- **Functions**: `sarai_chinwag_track_post_view()`, `sarai_chinwag_get_post_views($post_id)`

### Layout Strategy
- **Full-Width Pages**: Home, archives, search (sidebars removed for 4-column grid)
- **Sidebar Pages**: Single posts, recipes, pages (maintains discovery widgets)
- **Responsive Grid**: 4 columns (desktop) → 3 (laptop) → 2 (tablet) → 1 (mobile)
- **Filter Persistence**: AJAX Load More preserves active sort and type filters

## Badge-Breadcrumb Navigation System

### Single Posts/Recipes
- **Badge Navigation**: Clickable category (blue) and tag (pink) badges above post title
- **No Home Link**: Random icon and underlined site title serve this purpose
- **Function**: `sarai_chinwag_post_badges()` displays primary category + up to 3 tags

### Archive Pages
- **Traditional Breadcrumbs**: Text-based hierarchy (Home > Category/Tag/Search)
- **Context Aware**: Different patterns for category, tag, search, author, date archives
- **Function**: `sarai_chinwag_archive_breadcrumbs()` generates appropriate breadcrumb trail

## Build System

### Production Build
- **build.sh**: Creates optimized production package with versioned ZIP file
- **VSCode Integration**: .vscode/tasks.json provides build commands (Ctrl/Cmd+Shift+P → "Tasks: Run Task")
- **Output Location**: /dist directory contains clean theme and saraichinwag.zip
- **Exclusions**: Removes /docs, .git, build.sh, .vscode, and .md files from production
- **Automatic Versioning**: Extracts version from style.css for consistent packaging

### Development Commands
```bash
# Create production build
./build.sh

# VSCode tasks available:
# - Build Theme (default)
# - Clean Build 
# - Build and Clean (sequence)
# - Test Build Script (dry run)
```

### Development Workflow
1. Edit PHP, CSS, or JS files directly for development
2. Changes are immediately reflected (CSS/JS have dynamic versioning via `filemtime()`)
3. Run build.sh when ready for production deployment
4. Install dist/saraichinwag.zip in WordPress for production

### Testing & Debugging
- Test functionality directly in WordPress environment
- Use WordPress debugging (`WP_DEBUG`) for PHP errors
- Browser developer tools for JavaScript debugging
- AJAX debugging: Check Network tab for `/wp-admin/admin-ajax.php` requests
- Cache debugging: Clear object cache if changes don't appear (`wp_cache_flush()`)

### Common Development Tasks
- **Clear caches**: Delete all wp_cache_* entries when developing cached functionality
- **Test random functionality**: Use `/random-post`, `/random-recipe`, `/random-all` URLs
- **Image gallery testing**: Test category/tag image archives with `/category/name/images/` URLs
- **AJAX testing**: Verify nonce generation and validation for rating system and filter bar
- **Recipe toggle testing**: Enable/disable recipes in Settings → Theme Settings to test universal theme functionality
- **Contact form testing**: Use `[sarai_contact_form]` shortcode to embed contact forms and test Turnstile integration

## Code Conventions

- Follow WordPress coding standards
- Use `wp_remote_get()` instead of cURL functions  
- All output should be run through escaping functions (`esc_url()`, `esc_html()`, `esc_attr()`, etc.)
- Nonce verification required for AJAX requests using `wp_verify_nonce()`
- Use `get_template_directory()` and `get_template_directory_uri()` for file paths
- Dynamic versioning for static assets using `filemtime()` for cache busting
- Always call `wp_reset_postdata()` after custom post queries using `setup_postdata()`
- Sanitize all `$_POST` data with appropriate WordPress functions (`sanitize_text_field()`, etc.)
- Use wp_cache_* functions with appropriate cache groups for expensive queries (1 hour for random posts, 24 hours for Google Fonts, view counter caching)
- Use consolidated PHP organization in functions.php with logical function grouping
- JavaScript should use WordPress i18n (`wp.i18n`) for user-facing messages

## Footer Architecture

### Simplified Footer Design
The footer has been streamlined for better user experience and SEO performance:

**Core Elements:**
- **Surprise Me Button**: Random content discovery with shuffle icon
- **Pinterest Follow Button**: Dynamic integration using admin-configured username
- **Footer Menu**: WordPress nav menu support for footer navigation
- **Site Information**: Copyright notice with Amazon affiliate disclosure

**SEO Benefits:**
- **Reduced Link Bloat**: Eliminated category/tag clouds that created 130+ redundant links on every page
- **Improved Crawl Efficiency**: Search engines focus on primary content instead of repetitive footer links
- **Clean Information Architecture**: Footer now serves user navigation rather than SEO manipulation
- **Pinterest Integration**: Maintains valuable social media connectivity

**Technical Implementation:**
- Dynamic Pinterest username from admin settings (`sarai_chinwag_pinterest_username`)
- Clean Bootstrap Icons SVG for Pinterest logo (16x16 viewBox)
- Enhanced Pinterest save button functionality via `inc/assets/js/pinterest.js`
- Proper `rel="noopener noreferrer"` attributes for external links
- Translation-ready text with `translate="no"` attributes for proper names

## Performance Notes

**Object Caching Architecture:**
- **wp_cache_* functions** replace transients throughout theme for better performance
- **Cache Groups**: `sarai_chinwag_random`, `sarai_chinwag_related`, `sarai_chinwag_fonts`, `sarai_chinwag_views`, `sarai_chinwag_sidebar`, `sarai_chinwag_images`
- **Dynamic cache versioning** using `wp_cache_get_last_changed()` for content-dependent caches
- **Limited query results** (max 500 posts) to prevent memory issues in large sites

**Specific Optimizations:**
- Random posts cached for 1 hour with rotation system to reduce `orderby => 'rand'` queries
- Related content cached for 15 minutes per post
- Google Fonts API responses cached for 24 hours
- Editor font integration with conditional loading based on customizer selections
- View counter system uses efficient wp_cache with `sarai_chinwag_views` cache group
- Sidebar widgets cached for 15 minutes
- Image gallery system cached for 2 hours with `sarai_chinwag_images` cache group
- Dual cache strategy: Image data caches (30 limit) separate from image count caches (99999 limit)
- Centralized cache invalidation via `sarai_chinwag_clear_all_image_count_caches()` for media changes
- Media attachment hooks (`add_attachment`, `edit_attachment`, `delete_attachment`) trigger automatic cache clearing
- CSS uses custom properties for dynamic styling instead of inline styles
- All queries use proper WordPress functions and avoid direct database access

## Universal Theme Usage

### Recipe Toggle Feature
The theme includes a "Disable Recipe Functionality" setting in **Settings → Theme Settings** that allows complete control over recipe features:

**When Recipes Enabled (Default):**
- Recipe post type registered and available
- Recipe-specific templates and widgets active
- Rating system functional
- Embedded Schema.org recipe markup applied
- Recipe filtering in archives
- Random recipe functionality

**When Recipes Disabled:**
- All recipe functionality completely hidden
- Theme operates as standard blog theme
- Existing recipe posts remain accessible via direct URL only
- Clean, blog-focused user experience
- No recipe-specific scripts or styles loaded

### Helper Function
Use `sarai_chinwag_recipes_disabled()` to check recipe status in custom code:
```php
if (!sarai_chinwag_recipes_disabled()) {
    // Recipe-specific code here
}
```

### Key Function Reference
- `sarai_chinwag_recipes_disabled()`: Check if recipes are disabled
- `sarai_chinwag_set_default_recipe_rating($post_id, $post, $update)`: Automatically assign default 5-star rating to new recipes
- `sarai_chinwag_apply_default_ratings_to_existing()`: Retroactively apply default ratings to existing recipes without ratings
- `sarai_chinwag_track_post_view($post_id)`: Increment view counter
- `sarai_chinwag_get_post_views($post_id)`: Get view count
- `sarai_chinwag_extract_images_from_term($term, $taxonomy)`: Extract images from term
- `sarai_chinwag_get_accurate_term_image_count($term_id, $taxonomy)`: Get precise image counts for archive image mode links
- `sarai_chinwag_get_site_wide_image_count()`: Count all images across site for homepage image mode link
- `sarai_chinwag_get_search_image_count($search_query)`: Count images for search result image mode link
- `sarai_chinwag_clear_all_image_count_caches($post_id)`: Centralized function to clear all image count caches when media changes
- `sarai_chinwag_clear_image_cache_on_media_change($attachment_id)`: Clear image caches on attachment add/edit/delete
- `sarai_chinwag_post_badges()`: Display category/tag badges
- `sarai_chinwag_archive_breadcrumbs()`: Generate breadcrumb navigation
- `sarai_chinwag_gallery_discovery_badges()`: Display gallery discovery badges with image counts
- `sarai_chinwag_display_featured_image_as_block()`: Display featured image as Gutenberg block
- `sarai_chinwag_load_template()`: AJAX endpoint for loading template parts
- `sarai_chinwag_set_content_language_header()`: Set Content-Language HTTP header

## Internationalization & Browser Compatibility

**Language Support:**
- **Content-Language HTTP header**: Automatically set to 'en' to help browsers determine when to offer translation services
- **Language attributes**: Dynamic language attributes in template parts using `get_locale()` for proper content marking
- **Translation ready**: Full i18n support with `load_theme_textdomain()` and translatable strings throughout
- **Browser translation detection**: Helps browsers like Chrome and Edge determine when to show translation options

**Implementation Details:**
- Header function `sarai_chinwag_set_content_language_header()` sets HTTP headers before content output
- Template parts include `lang` attributes on content containers
- All user-facing strings use WordPress translation functions with proper text domains

## Security Notes

- All user input is properly sanitized using WordPress functions
- All output is properly escaped with `esc_html()`, `esc_url()`, `esc_attr()`, etc.
- API keys stored in database options via `get_option()`, never hardcoded
- Rating system uses WordPress nonce verification for CSRF protection
- AJAX endpoints include proper nonce verification and validation
- Client-side localStorage provides graceful degradation if server requests fail

## Theme Information

**Current Version**: 2.2 (WordPress Editor Integration & Contact Form System)  
**Live Demo**: [saraichinwag.com](https://saraichinwag.com)  
**Developer**: Chris Huber ([chubes.net](https://chubes.net))

### Recent Major Updates (v2.2.1)
- **Image Count Cache Invalidation**: Centralized cache clearing system for immediate count updates on media changes
- **Dual Cache Strategy**: Separate image data caches (30 limit) and image count caches (99999 limit) for optimal performance
- **Media Attachment Hooks**: Automatic cache invalidation on attachment add/edit/delete via WordPress hooks
- **Accurate Image Counts**: Archive image mode links now support tens of thousands of images with precise counts
- **Cache Management Functions**: New `sarai_chinwag_clear_all_image_count_caches()` and `sarai_chinwag_clear_image_cache_on_media_change()` functions

### Recent Major Updates (v2.2)
- **Default 5-Star Rating System**: New recipes automatically receive 5.0 rating with 1 review count upon publication for immediate visibility in popularity sorting
- **WordPress Editor Font Integration**: Consistent font experience between Block Editor, Classic Editor, and frontend
- **Contact Form System**: Complete AJAX-powered contact forms with Cloudflare Turnstile bot protection, email notifications, and shortcode integration
- **Gallery Discovery Badges**: Intelligent gallery links on single posts showing related image galleries with accurate counts
- **Image Anchoring**: Automatic anchorable spans on images for deep linking and improved navigation
- **Footer Architecture Simplification**: Removed category/tag clouds for better UX and SEO (130+ link reduction)
- **Pinterest Icon Enhancement**: Clean Bootstrap Icons SVG with proper 16x16 viewBox
- **Object Caching Evolution**: New cache groups for views and sidebar, continued wp_cache_* migration
- **Image Optimization**: Consistent 'grid-thumb' usage across all templates for better performance
- **SEO Improvements**: Reduced footer link bloat, improved crawl efficiency
- **Editor CSS Integration**: Dynamic scaling and font loading in WordPress editors
- **Performance Optimizations**: Enhanced caching strategies with proper cache group segregation