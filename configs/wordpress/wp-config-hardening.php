<?php
/**
 * WordPress Hardened Configuration Template
 *
 * Repository: wsd-wordpress-security
 * Maintained as part of Web Stack Defense — https://www.webstackdefense.com
 *
 * This is a reference template, not a drop-in replacement.
 *
 * Every directive below is commented with the reason it exists. Read each block
 * before adopting it. Some directives will break legitimate functionality in
 * specific environments. The comments call out where that is likely.
 *
 * Sections:
 *   1.  Database connection
 *   2.  Authentication keys and salts
 *   3.  Database table prefix
 *   4.  File system and update controls
 *   5.  Editor and update lockdown
 *   6.  Debug and error reporting
 *   7.  Cron behavior
 *   8.  Post revisions and autosave
 *   9.  SSL and HTTPS enforcement
 *   10. Trusted proxy and reverse proxy handling
 *   11. WordPress address and site URL overrides
 *   12. Memory and resource limits
 *   13. Multisite considerations (commented out by default)
 *   14. Custom content and uploads location (commented out by default)
 *   15. Final require — do not move
 *
 * Sanitization note: all values that look like real credentials are placeholders.
 * Replace every CHANGE_ME_ before deploying. Do not commit a populated copy.
 */


/* ----------------------------------------------------------------------------
 * 1. Database connection
 * --------------------------------------------------------------------------
 * Standard WordPress database connection settings. The values themselves are
 * not a security control, but two things in this section are:
 *
 *   - Use a database user that has access ONLY to the WordPress database.
 *     Never use the MySQL root user.
 *
 *   - Use a strong, unique password for this database user. Database
 *     credentials in wp-config.php are the highest-value secret on the
 *     filesystem after the auth keys.
 *
 *   - Where possible, keep the database on a separate host (DB_HOST) and
 *     restrict its bind address to the web server only.
 * -------------------------------------------------------------------------- */

define( 'DB_NAME',     'CHANGE_ME_database_name' );
define( 'DB_USER',     'CHANGE_ME_database_user' );
define( 'DB_PASSWORD', 'CHANGE_ME_database_password' );
define( 'DB_HOST',     'localhost' );
define( 'DB_CHARSET',  'utf8mb4' );
define( 'DB_COLLATE',  '' );


/* ----------------------------------------------------------------------------
 * 2. Authentication keys and salts
 * --------------------------------------------------------------------------
 * These eight values are used to sign and encrypt WordPress authentication
 * cookies and nonces. They are critical secrets.
 *
 *   - Generate fresh values from https://api.wordpress.org/secret-key/1.1/salt/
 *     The official endpoint returns eight random values formatted correctly.
 *
 *   - Rotate these values during incident response. Rotation invalidates every
 *     existing login session, forcing all users to log in again. That is the
 *     intended behavior after a suspected compromise.
 *
 *   - Never commit real values to version control. The placeholder values
 *     below are intentionally not random.
 * -------------------------------------------------------------------------- */

define( 'AUTH_KEY',         'CHANGE_ME_unique_phrase_here' );
define( 'SECURE_AUTH_KEY',  'CHANGE_ME_unique_phrase_here' );
define( 'LOGGED_IN_KEY',    'CHANGE_ME_unique_phrase_here' );
define( 'NONCE_KEY',        'CHANGE_ME_unique_phrase_here' );
define( 'AUTH_SALT',        'CHANGE_ME_unique_phrase_here' );
define( 'SECURE_AUTH_SALT', 'CHANGE_ME_unique_phrase_here' );
define( 'LOGGED_IN_SALT',   'CHANGE_ME_unique_phrase_here' );
define( 'NONCE_SALT',       'CHANGE_ME_unique_phrase_here' );


/* ----------------------------------------------------------------------------
 * 3. Database table prefix
 * --------------------------------------------------------------------------
 * Changing the default `wp_` prefix is a low-value hardening step that is
 * widely repeated in WordPress security advice. It does not stop a competent
 * attacker who already has database access, and it complicates restoration
 * from clean WordPress installs.
 *
 * It is included here because it adds some friction to automated SQL injection
 * payloads that target the default schema names. The realistic value is small.
 *
 * If you set this on an existing site, you must also rename all tables and
 * update the `options` table records that reference the prefix. Do not change
 * this value on a live site without a plan.
 * -------------------------------------------------------------------------- */

$table_prefix = 'wp_';


/* ----------------------------------------------------------------------------
 * 4. File system and update controls
 * --------------------------------------------------------------------------
 * These directives control how WordPress writes to the filesystem during
 * updates, plugin installs, and theme installs.
 *
 * FS_METHOD = 'direct' tells WordPress to write files directly using PHP's
 *   file handling functions rather than prompting for FTP credentials. This
 *   requires that the web server user has write access to the necessary
 *   directories. It avoids the security risk of storing FTP credentials in
 *   the database or in PHP sessions.
 *
 * DISALLOW_FILE_MODS = true prevents the WordPress dashboard from installing,
 *   updating, or deleting plugins and themes. This is appropriate for
 *   environments where all plugin and theme changes are managed through
 *   version control or deployment pipelines.
 *
 *   Setting this to true means dashboard users CANNOT install plugins, even
 *   admins. Auto-updates also stop. If you set this, you must have an
 *   external process for keeping plugins and themes patched.
 * -------------------------------------------------------------------------- */

define( 'FS_METHOD', 'direct' );

// Uncomment ONLY if you manage plugin and theme updates outside the dashboard.
// define( 'DISALLOW_FILE_MODS', true );


/* ----------------------------------------------------------------------------
 * 5. Editor and update lockdown
 * --------------------------------------------------------------------------
 * DISALLOW_FILE_EDIT removes the in-dashboard plugin and theme file editors.
 *   Those editors are a common post-compromise attack tool: an attacker who
 *   gains admin access uses the editor to inject malicious code into an
 *   existing plugin file rather than uploading a new file that might trigger
 *   detection. Disabling the editor closes that path.
 *
 *   This setting is safe to enable on virtually every production WordPress
 *   site. Developers who need to edit files should use SSH, SFTP, or a
 *   deployment pipeline rather than the dashboard editor.
 *
 * AUTOMATIC_UPDATER_DISABLED disables automatic background updates entirely.
 *   By default, WordPress automatically applies minor core updates and
 *   security releases. Disabling this requires an external process for
 *   keeping core patched.
 *
 *   Most environments should LEAVE automatic minor updates enabled. The
 *   line below is commented out for that reason.
 *
 * WP_AUTO_UPDATE_CORE controls major version updates separately.
 *   Setting it to 'minor' (the default) applies minor and security updates
 *   automatically but holds major versions for manual review. This is the
 *   recommended setting.
 * -------------------------------------------------------------------------- */

define( 'DISALLOW_FILE_EDIT', true );

// Leave automatic security updates enabled in most environments.
// define( 'AUTOMATIC_UPDATER_DISABLED', true );

define( 'WP_AUTO_UPDATE_CORE', 'minor' );


/* ----------------------------------------------------------------------------
 * 6. Debug and error reporting
 * --------------------------------------------------------------------------
 * Debug output in production is a serious information disclosure risk.
 * Stack traces, database queries, file paths, and plugin internals exposed
 * to a public visitor give an attacker exactly the reconnaissance they need.
 *
 *   WP_DEBUG = false           Do not show errors to visitors.
 *   WP_DEBUG_LOG = true        Log errors to wp-content/debug.log instead.
 *   WP_DEBUG_DISPLAY = false   Do not echo errors to the browser.
 *   SCRIPT_DEBUG = false       Use minified JS and CSS in production.
 *
 * The debug log file MUST be excluded from public access at the web server
 * level. The .htaccess and Nginx configs in this repository handle that.
 *
 * In a development environment, set WP_DEBUG and WP_DEBUG_DISPLAY to true.
 * Never do that in production.
 * -------------------------------------------------------------------------- */

define( 'WP_DEBUG',          false );
define( 'WP_DEBUG_LOG',      true );
define( 'WP_DEBUG_DISPLAY',  false );
define( 'SCRIPT_DEBUG',      false );

// Suppress PHP errors from being printed to the response.
@ini_set( 'display_errors', '0' );


/* ----------------------------------------------------------------------------
 * 7. Cron behavior
 * --------------------------------------------------------------------------
 * WordPress runs scheduled tasks through wp-cron.php, which is triggered by
 * incoming page requests. On low-traffic sites, scheduled tasks can be
 * delayed. On high-traffic sites, wp-cron.php gets called constantly and
 * adds load.
 *
 * The recommended pattern is:
 *
 *   1. Disable the default request-triggered cron.
 *   2. Schedule wp-cron.php through the system crontab to run every 5
 *      minutes (or whatever frequency suits the site).
 *
 * This makes cron predictable, reduces unnecessary load, and prevents
 * abuse of wp-cron.php as a DoS amplification target.
 *
 * System cron entry example (run every 5 minutes, redirect output):
 *
 *   */5 * * * * curl -s https://example.com/wp-cron.php?doing_wp_cron > /dev/null 2>&1
 *
 * Or for sites where wp-cron.php is restricted to localhost:
 *
 *   */5 * * * * /usr/bin/php /var/www/html/wp-cron.php > /dev/null 2>&1
 * -------------------------------------------------------------------------- */

define( 'DISABLE_WP_CRON', true );


/* ----------------------------------------------------------------------------
 * 8. Post revisions and autosave
 * --------------------------------------------------------------------------
 * These are performance and storage hygiene settings rather than security
 * settings, but unbounded revision growth on large editorial sites becomes
 * a denial-of-service risk against the database. Capping revisions reduces
 * database bloat and improves restore times.
 *
 *   WP_POST_REVISIONS = 10     Keep the last 10 revisions per post.
 *   AUTOSAVE_INTERVAL = 300    Autosave every 5 minutes instead of 60s.
 *   EMPTY_TRASH_DAYS = 7       Empty trashed posts after 7 days.
 * -------------------------------------------------------------------------- */

define( 'WP_POST_REVISIONS', 10 );
define( 'AUTOSAVE_INTERVAL', 300 );
define( 'EMPTY_TRASH_DAYS',  7 );


/* ----------------------------------------------------------------------------
 * 9. SSL and HTTPS enforcement
 * --------------------------------------------------------------------------
 * FORCE_SSL_ADMIN forces all admin area requests over HTTPS. This is
 * mandatory for any production WordPress site. There is no scenario in 2026
 * where the admin area should be accessible over plaintext HTTP.
 *
 * The site URL itself (set in section 11 below, or in the database) should
 * also use https://.
 *
 * If you are behind a reverse proxy or CDN that terminates TLS, see
 * section 10 for the X-Forwarded-Proto handling.
 * -------------------------------------------------------------------------- */

define( 'FORCE_SSL_ADMIN', true );


/* ----------------------------------------------------------------------------
 * 10. Trusted proxy and reverse proxy handling
 * --------------------------------------------------------------------------
 * When WordPress runs behind a CDN, WAF, or reverse proxy (Cloudflare,
 * AWS CloudFront, Nginx reverse proxy, etc.), the connection from the
 * proxy to WordPress is often plain HTTP even though the external request
 * was HTTPS. The proxy signals this via an X-Forwarded-Proto header.
 *
 * Without this block, WordPress will see plain HTTP, generate http://
 * URLs, and trigger redirect loops or mixed content warnings.
 *
 * This block tells WordPress to trust the X-Forwarded-Proto header.
 *
 * IMPORTANT: Only enable this if your origin server is actually reachable
 * only through a trusted proxy. If a visitor can reach the origin directly,
 * they can spoof X-Forwarded-Proto to confuse WordPress. The .htaccess and
 * Nginx configurations in this repository include origin lockdown patterns
 * for Cloudflare-fronted deployments.
 * -------------------------------------------------------------------------- */

if ( isset( $_SERVER['HTTP_X_FORWARDED_PROTO'] )
    && 'https' === $_SERVER['HTTP_X_FORWARDED_PROTO']
) {
    $_SERVER['HTTPS'] = 'on';
}


/* ----------------------------------------------------------------------------
 * 11. WordPress address and site URL overrides
 * --------------------------------------------------------------------------
 * Setting WP_HOME and WP_SITEURL here forces WordPress to use these values
 * regardless of what is stored in the database. This is useful for:
 *
 *   - Locking the site URL after a known-good configuration is established
 *   - Preventing URL tampering attacks where an attacker modifies the
 *     siteurl option in the database to redirect logins
 *   - Making environment-specific URLs (staging, production) explicit
 *
 * Once these are set in wp-config.php, the URL fields in Settings → General
 * become read-only in the dashboard. That is intentional.
 * -------------------------------------------------------------------------- */

define( 'WP_HOME',    'https://example.com' );
define( 'WP_SITEURL', 'https://example.com' );


/* ----------------------------------------------------------------------------
 * 12. Memory and resource limits
 * --------------------------------------------------------------------------
 * WordPress memory limits affect what plugins can do and how large media
 * uploads can be. They are not strictly security settings, but unbounded
 * memory limits can be abused by malicious plugins or compromised admin
 * accounts to mine cryptocurrency or run expensive operations.
 *
 *   WP_MEMORY_LIMIT       — Frontend memory ceiling
 *   WP_MAX_MEMORY_LIMIT   — Admin and cron memory ceiling
 *
 * Set these to the lowest values that still allow your site to function.
 * The values below are reasonable defaults for most sites.
 * -------------------------------------------------------------------------- */

define( 'WP_MEMORY_LIMIT',     '256M' );
define( 'WP_MAX_MEMORY_LIMIT', '512M' );


/* ----------------------------------------------------------------------------
 * 13. Multisite considerations
 * --------------------------------------------------------------------------
 * Multisite installations require additional directives. These are commented
 * out by default. If you are running multisite, uncomment and configure.
 *
 * Multisite has its own hardening considerations beyond the scope of this
 * baseline file. See the multisite hardening checklist in this repository.
 * -------------------------------------------------------------------------- */

// define( 'WP_ALLOW_MULTISITE', true );
// define( 'MULTISITE',          true );
// define( 'SUBDOMAIN_INSTALL',  false );
// define( 'DOMAIN_CURRENT_SITE','example.com' );
// define( 'PATH_CURRENT_SITE',  '/' );
// define( 'SITE_ID_CURRENT_SITE', 1 );
// define( 'BLOG_ID_CURRENT_SITE', 1 );


/* ----------------------------------------------------------------------------
 * 14. Custom content and uploads location
 * --------------------------------------------------------------------------
 * Moving wp-content to a non-default location is sometimes recommended as a
 * hardening step. It breaks many plugins and themes that hard-code paths,
 * and it does not stop a serious attacker. The realistic security value
 * is small, the maintenance cost is real.
 *
 * These directives are commented out and shown only for completeness.
 *
 * If you do move wp-content, the web server configuration must also be
 * updated so the new directory is served correctly and the .htaccess or
 * Nginx rules in this repository must be adapted to the new path.
 * -------------------------------------------------------------------------- */

// define( 'WP_CONTENT_DIR', dirname( __FILE__ ) . '/custom-content' );
// define( 'WP_CONTENT_URL', 'https://example.com/custom-content' );


/* ----------------------------------------------------------------------------
 * 15. Final require — do not move
 * --------------------------------------------------------------------------
 * The two lines below set up the ABSPATH constant and load the WordPress
 * bootstrap file. Nothing should be placed below them. Adding directives
 * after the require statement will be ignored.
 * -------------------------------------------------------------------------- */

if ( ! defined( 'ABSPATH' ) ) {
    define( 'ABSPATH', __DIR__ . '/' );
}

require_once ABSPATH . 'wp-settings.php';
