# Sarai Chinwag Theme

Anti-chronological WordPress theme — randomization-first design that encourages serendipitous content discovery.

## What It Does

A WordPress theme built for exploration over chronology:

- **Random by default** — Home and archives shuffle content instead of date-sorting
- **4-column grid** — Maximizes content discovery on browse pages
- **Image galleries** — `/images/` suffix transforms any archive into a visual gallery
- **Recipe support** — Optional recipe post type with Schema.org markup and ratings

## How It Works

```
┌─────────────────────────────────────────────────────────────┐
│                      BROWSE PAGES                           │
│  ┌─────┐ ┌─────┐ ┌─────┐ ┌─────┐                          │
│  │     │ │     │ │     │ │     │  ← 4-column grid         │
│  │ RND │ │ RND │ │ RND │ │ RND │  ← Randomized content    │
│  │     │ │     │ │     │ │     │                          │
│  └─────┘ └─────┘ └─────┘ └─────┘                          │
│                                                             │
│  [Random] [Popular] [Recent] [Oldest]  ← Filter bar        │
│  [Load More...]                                             │
└─────────────────────────────────────────────────────────────┘
         │
         ▼
┌─────────────────────────────────────────────────────────────┐
│                      SINGLE POST                            │
│  ┌─────────────────────┐  ┌──────────┐                     │
│  │                     │  │ Sidebar  │                     │
│  │   Full Content      │  │ Discovery│                     │
│  │   + Badge Nav       │  │ Widgets  │                     │
│  │                     │  │          │                     │
│  └─────────────────────┘  └──────────┘                     │
└─────────────────────────────────────────────────────────────┘
```

## Features

| Feature | Description |
|---------|-------------|
| **Filter Bar** | Sort by Random, Popular, Recent, Oldest with AJAX |
| **Image Mode** | `/category/birds/images/` shows gallery view |
| **Load More** | Infinite scroll preserving filter state |
| **View Counter** | Async tracking for popularity sorting |
| **Google Fonts** | Full library access via Customizer |

## Content Modes

| Mode | Description |
|------|-------------|
| **Blog** | Standard posts with randomization |
| **Recipe Site** | Full recipe support with ratings, Schema.org |
| **Image Gallery** | Any archive + `/images/` suffix |

## Random Endpoints

| URL | Returns |
|-----|---------|
| `/random-post` | Random blog post |
| `/random-recipe` | Random recipe |
| `/random-all` | Random any content |

## Requirements

- WordPress 6.0+
- PHP 8.0+
- Google Fonts API key (optional, for font customization)

## Installation

```bash
# Development
git clone [repo] wp-content/themes/saraichinwag/

# Production build
./build.sh  # Creates dist/saraichinwag.zip
```

## Configuration

1. **Theme Settings** (`Settings → Theme Settings`)
   - Google Fonts API key
   - Recipe toggle (enable/disable)
   - Contact form settings

2. **Customizer** (`Appearance → Customize → Typography`)
   - Heading font selection
   - Body font selection
   - Size scaling (1-100%)

## Development

```
saraichinwag/
├── inc/                    # PHP modules
│   ├── admin/             # Settings panels
│   ├── contact/           # Contact form system
│   ├── queries/           # Query modifications
│   │   └── image-mode/    # Gallery system
│   └── assets/            # CSS/JS by feature
├── template-parts/        # Reusable components
├── js/                    # Frontend scripts
└── build.sh              # Production packager
```

## Documentation

- [AGENTS.md](AGENTS.md) — Technical architecture for contributors

## Live Demo

[saraichinwag.com](https://saraichinwag.com)

---

**Version**: 2.2.2  
**Author**: [Chris Huber](https://chubes.net)  
**License**: Personal use / white-label (commercial requires permission)
