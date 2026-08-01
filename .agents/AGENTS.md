# AgriSync — Project Rules & Coding Standards

## Technology Stack (STRICT)
- **Backend:** PHP 8.x (vanilla, no frameworks)
- **Database:** MySQL 8.x with PDO (prepared statements ONLY)
- **Frontend:** HTML5 + Bootstrap 5.3 + Vanilla CSS + Vanilla JavaScript
- **AJAX:** Vanilla `fetch()` API (no jQuery)
- **AI Integration:** Google Gemini API via PHP cURL
- **Icons:** Bootstrap Icons (CDN)
- **Charts:** Chart.js 4.x (CDN)
- **Font:** Google Fonts — Inter

## Design System

### Color Palette
| Token | Hex | Usage |
|---|---|---|
| `--clr-primary` | `#2D6A4F` | Primary buttons, headers, active nav |
| `--clr-primary-dark` | `#1B4332` | Hover states, footer, dark sections |
| `--clr-primary-light` | `#40916C` | Secondary elements, borders |
| `--clr-accent` | `#95D5B2` | Highlights, badges, success states |
| `--clr-accent-light` | `#D8F3DC` | Light backgrounds, card accents |
| `--clr-bg` | `#F8F9FA` | Page background |
| `--clr-surface` | `#FFFFFF` | Card backgrounds |
| `--clr-text` | `#212529` | Body text |
| `--clr-text-muted` | `#6C757D` | Secondary text |
| `--clr-danger` | `#DC3545` | Errors, destructive actions |
| `--clr-warning` | `#FFC107` | Warnings, pending states |
| `--clr-info` | `#0DCAF0` | Informational badges |

### Typography
- **Headings:** Inter, 600-700 weight
- **Body:** Inter, 400 weight
- **Small:** 0.875rem
- **Base:** 1rem
- **H1:** 1.75rem, H2: 1.5rem, H3: 1.25rem

### Spacing
- Use Bootstrap spacing utilities: `mb-3`, `p-4`, `gap-3`
- Card padding: `p-4`
- Section margin: `mb-4`

### Components
- Cards: `border-radius: 12px`, `box-shadow: 0 2px 12px rgba(0,0,0,0.08)`
- Buttons: `border-radius: 8px`, min-height `44px`
- Inputs: `border-radius: 8px`, use Bootstrap form classes
- Tables: `.table .table-hover` with custom header color
- Modals: Bootstrap modals with custom header color

### Animations
- Hover transitions: `transition: all 0.2s ease`
- Card hover: subtle `translateY(-2px)` + shadow increase
- Page load: fade-in on dashboard cards
- AI processing: custom spinner animation
- Toast notifications: slide-in from top-right

## PHP Coding Rules

1. **ALWAYS use PDO with prepared statements** — NEVER concatenate user input into SQL
2. **Start every protected page** with `require_once '../config/session.php';` and `require_once '../auth/auth_check.php';`
3. **Return JSON** from all files in `/api/` directory
4. **Use `password_hash()` with `PASSWORD_BCRYPT`** for passwords
5. **Use `htmlspecialchars()`** when outputting user data in HTML
6. **Naming:** snake_case for variables, functions, and file names
7. **Constants:** UPPER_SNAKE_CASE in `config/constants.php`
8. **Error handling:** Never expose raw PHP errors to users; use try-catch with user-friendly messages
9. **File structure:** Each page includes `header.php` and `footer.php`
10. **API responses format:** `{"success": true/false, "data": {...}, "error": "message"}`

## JavaScript Coding Rules

1. **Use `fetch()` API** for all AJAX calls — no jQuery
2. **Naming:** camelCase for variables and functions
3. **Use `const` and `let`** — never `var`
4. **Show loading states** for every async operation
5. **Handle errors gracefully** — show toast notifications
6. **DOMContentLoaded** for initialization

## Database Rules

1. All table names: snake_case, plural (e.g., `harvest_listings`)
2. All column names: snake_case (e.g., `created_at`)
3. Every table MUST have `id` (INT AUTO_INCREMENT), `created_at`, `updated_at`
4. Use ENUM for status fields
5. Foreign keys with ON DELETE CASCADE where appropriate
6. Index all foreign key columns and frequently filtered columns

## Git Rules

1. **Branch naming:** `feature/TASK-XX-short-description`
2. **Commit messages:** `[TASK-XX] Brief description of change`
3. **One task per PR** — keep PRs small and focused
4. **Never commit API keys** — use `config/constants.example.php`
5. **Always test locally** before pushing

## File Organization

```
agrisync/
├── config/          # Database, constants, session setup
├── auth/            # Login, register, logout, auth check
├── farmer/          # Farmer dashboard and pages
├── business/        # Business dashboard and pages
├── admin/           # Admin panel pages
├── api/             # AJAX JSON endpoints
├── agents/          # AI agent PHP logic
├── includes/        # Shared header, footer, sidebar, functions
├── assets/css/      # Custom styles
├── assets/js/       # Custom JavaScript
├── assets/img/      # Images and icons
├── sql/             # Database schema and seed data
└── uploads/         # User-uploaded files
```
