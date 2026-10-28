
## Version 4 UI updates
- Compact left-aligned navigation and account actions
- Create-account action replaced by a + icon
- Short single-line navigation labels: Freebies and Guide
- Faster carousel with no staggered content entrance delay
- Wider desktop navbar usage with the logo closer to the left edge
# Student Exchange Hub Pro

A responsive student marketplace built with PHP, MySQL/MySQLi, HTML, CSS, JavaScript and jQuery. The implementation stays within the technologies covered by the supplied course slides.

## Included features

- Student registration and sign-in using PHP sessions
- Password hashing and verification
- CSRF tokens for state-changing forms
- Prepared statements for database operations
- Browse, search, filter, sort and paginate available listings
- Listing details and seller contact
- Create, edit, hide, mark sold and delete your own listings
- Profile editing and dashboard
- Student-to-student message conversations
- Responsive navigation and mobile-friendly layouts
- Server-side validation and HTML output escaping
- SQL schema separated from application code; no automatic demo account creation

## Syllabus mapping (BTCO23307 Web Development)

| Unit | Topic from syllabus | Where it is used |
|---|---|---|
| 1 | Client-server, XAMPP, navigation, sitemap, planning | `how_it_works.php`, `sitemap.php`, `sitemap.xml`, Bootstrap navbar in `header.php` |
| 2 | HTML5: lists, tables, forms and input types, audio/video, iframe, meta tags | `about.php` (video, audio, iframe map, table, ol/ul/dl, date/range/tel/email inputs), meta tags in `head_assets.php` |
| 2 | CSS: variables, box model, positioning, animations, media queries, wildcard selectors (`*`, `^=`, `$=`) | `themes.css` (variables), `style.css`, `enhance.css` (last section) |
| 3 | Bootstrap CDN, grid, navbar, dropdowns, carousel, collapse, forms | CDN link in `head_assets.php`; grid/carousel in `index.php`; navbar in `header.php` |
| 4 | JavaScript: variables, arrays, objects, functions, loops, popup boxes, validation, events, fetch API | `script.js` sections 5, 8, 9 |
| 4 | jQuery: selectors, events, DOM, effects, AJAX | `script.js` sections 1, 2, 6, 7, 10 (`$.ajax`, `$.getJSON`, `fadeIn`, `slideToggle`) |
| 5 | Form processing, query strings, cookies, sessions | `login.php`, `about.php`, `browse.php?category=...`, `db.php`, `set_theme.php`, `set_consent.php` |
| 5 | Database connectivity, CRUD, prepared statements, **stored procedures** | `add_product.php`, `edit_product.php`, `delete_product.php`; procedures in `stored_procedures.sql`, called from `db.php` (`marketplace_stats`, `search_titles`) |

### Importing the stored procedures

If you imported `setup.sql` earlier, open phpMyAdmin > the database > SQL tab and run `stored_procedures.sql`. The site still works without it (it falls back to normal queries), but the procedures are what the syllabus asks for.

## New in this update

- **Larger, easier-to-read text** across the whole site (base size 15px -> 17px, small labels scaled up).
- **Theme menu in the navbar**: Ocean and Ocean Dark. Stored in the session and, with consent, in a cookie (`seh_theme`).
- **Home page carousel**: 4 slides, autoplay with progress bar, arrows, dots, swipe and keyboard support, pauses on hover.
- **Cookies** (`set_consent.php`): consent banner (Accept all / Reject optional / Customize), a preferences dialog with Essential / Preferences / Statistics switches, and a floating cookie button plus footer link to reopen it. Preferences cookies: `seh_theme`, `seh_email`. Statistics cookie: `seh_last_visit`. `seh_consent` stores the choice.
- **Sessions**: login session, flash messages, CSRF token, theme, "Recently viewed" list on the home page, 30-minute idle timeout and session-ID rotation every 15 minutes.
- Refreshed visuals: gradients, glass sticky header, hover effects, reveal-on-scroll animations.

## Requirements

- PHP 8.1+ with `mysqli`, `mbstring` and `mysqlnd` enabled
- MySQL 8+ or a compatible MariaDB release
- Apache or another PHP-capable web server
- Internet connection for the Google-hosted jQuery file and Google Fonts used in the UI, or replace those references with local copies if the hosting environment requires fully self-hosted assets

## Local setup (XAMPP example)

1. Copy the `student_exchange_hub_pro` folder into `htdocs`.
2. Start Apache and MySQL from XAMPP.
3. Open phpMyAdmin and import `setup.sql`. The SQL creates the database and tables; it does not create demo users or demo listings.
4. Review `config.php`. The defaults (`127.0.0.1`, database user `root`, empty password) are for a typical local development setup only.
5. Open `http://localhost/student_exchange_hub_pro/`.
6. Create your first account through **Create account**.

If your MySQL setup uses different credentials, update `config.php` or set the `SEH_DB_HOST`, `SEH_DB_PORT`, `SEH_DB_NAME`, `SEH_DB_USER`, `SEH_DB_PASS` environment variables.

## Production deployment checklist

Do not deploy the local configuration unchanged.

1. Use HTTPS and configure the web server to redirect HTTP to HTTPS.
2. Create a dedicated MySQL user with access only to this application database; do not use MySQL root.
3. Set database credentials using hosting environment variables or a configuration file outside the public document root.
4. Set `SEH_APP_ENV=production` so database errors do not disclose internal details.
5. Remove write permissions from application files and keep backups of the database.
6. Configure server-side error logging and monitor it; never display PHP errors to visitors in production.
7. Configure rate limiting or hosting-level protections for login and registration attempts. Application-level login throttling is not included in this course-slide-based version.
8. Decide how the site administrator will handle reports, abusive users and suspicious listings; a moderation/reporting workflow is not included yet.
9. Set up email delivery and account recovery before relying on the site for real users. Password reset and email verification are not included.
10. Test registration, login/logout, CRUD operations, messages, authorization boundaries, and mobile layouts on the actual host.
11. Confirm PHP and database versions, backup/restore procedure, privacy notice, terms of use, and applicable legal requirements.

## Security notes and limitations

- Passwords are stored with PHP `password_hash()` and checked with `password_verify()`.
- SQL user input is passed using MySQLi prepared statements.
- State-changing forms include CSRF tokens. Listing deletion is POST-only.
- Session cookies use HttpOnly and SameSite=Lax; the Secure flag is enabled when PHP detects HTTPS.
- Server-side validation and HTML escaping are used.
- This is a substantially hardened course-project baseline, **not a security-audited or certified production service**. It still needs deployment-specific configuration, automated/manual security testing, abuse controls, account recovery, email verification and moderation before handling a public user base.
