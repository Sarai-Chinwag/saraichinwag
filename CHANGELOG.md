# Changelog

## [2.5.2] - 2026-02-18

### Fixed
- Load single.css on journal post type for proper layout
- Center breadcrumbs and journal-date on mobile

## [2.5.1] - 2026-02-16

- Show sidebar on journal archive page

## [2.5.0] - 2026-02-14

### Added
- Journal archive: list-style template, breadcrumbs, dates
- Decouple site title font with --font-site-title CSS variable and customizer setting
- Wire quizzes into filter bar alongside posts and recipes
- 3-color system: purple secondary, pink demoted to tertiary with --color-tertiary variable

### Changed
- Switch heading font from Gluten to Lora, remove uppercase headings

### Fixed
- Hide filter bar on journal archives
- Remove hardcoded font and color fallbacks

## [2.4.0] - 2026-02-14

- Add journal CPT with admin toggle, templates, and full site integration

## [2.3.0] - 2026-02-09

- Add missing sarai_chinwag_clear_all_image_count_caches() function to fix stale image counts in Redis cache
- Fix misleading doc comments that said '2-hour cache' when using YEAR_IN_SECONDS

## [2.2.20] - 2026-02-06

- Add GitHub and Moltbook to footer social links
- Fix social link styling with increased CSS specificity

## [2.2.19] - 2026-02-06

- Replace Pinterest follow button with social media icons (X, Instagram, Pinterest)

## [2.2.18] - 2026-02-05

- Active filter buttons: transparent border (highlight only, no extra border)

## [2.2.17] - 2026-02-05

- Fix filter button specificity - use button.filter-btn selector to ensure shape preserved on all states

## [2.2.9] - 2026-02-02

### Fixed
- Fixed: Footer menu items now wrap on mobile (added flex-wrap)

## [2.2.8] - 2026-02-02

### Fixed
- Fixed: Editor now uses Customizer-selected fonts (dynamic CSS variables)

## [2.2.7] - 2026-02-02

### Fixed
- Fixed: Responsive sidebar stacking on mobile (was staying 28% width)
- Fixed: Editor styles not loading - added root.css for CSS variables

## [2.2.6] - 2026-02-01

### Changed
- Changed: Moved 404 page styles to separate CSS file (inc/assets/css/404.css)

## [2.2.5] - 2026-02-01

### Added
- Added: 404 error page template with search, random post button, and popular posts

## [2.2.4] - 2026-02-01
- Initial release
