# WordPress File Permissions Reference

File and directory permissions for WordPress on Linux hosts. These values assume a standard LAMP or LEMP stack with PHP-FPM running as the `www-data` user (or equivalent).

## Permission Targets

These are the recommended permissions for a production WordPress installation. They balance the need for WordPress to update itself and accept uploads against the principle of least privilege.

| Path | Permissions | Owner |
|------|-------------|-------|
| `/var/www/html/` (web root) | `755` | `www-data:www-data` |
| All directories under web root | `755` | `www-data:www-data` |
| All files under web root | `644` | `www-data:www-data` |
| `wp-config.php` | `600` or `640` | `www-data:www-data` |
| `.htaccess` | `644` | `www-data:www-data` |
| `wp-content/uploads/` | `755` (recursive on directories) | `www-data:www-data` |
| `wp-content/plugins/` | `755` (directories), `644` (files) | `www-data:www-data` |
| `wp-content/themes/` | `755` (directories), `644` (files) | `www-data:www-data` |

## Why `600` or `640` for wp-config.php

`wp-config.php` contains database credentials and authentication keys. It should not be readable by any user except the web server user.

- `600` (read/write owner only) is safer but breaks shared-hosting environments where the web server runs as a separate user from the file owner.
- `640` (read/write owner, read group) is the right compromise on most managed hosting environments.
- `644` (the WordPress installer default) is too permissive — any local user on the server can read the file.

If you are on shared hosting and cannot set `600` or `640`, set the file to `640` and verify the group matches the web server user.

## Setting Permissions

The commands below set the recommended permissions across the WordPress installation. Run them from the WordPress root.

**Always take a backup before running recursive permission changes.**

```bash
# Set ownership for the entire WordPress directory tree.
sudo chown -R www-data:www-data /var/www/html

# Set directories to 755.
sudo find /var/www/html -type d -exec chmod 755 {} \;

# Set files to 644.
sudo find /var/www/html -type f -exec chmod 644 {} \;

# Lock down wp-config.php.
sudo chmod 640 /var/www/html/wp-config.php

# Verify the result.
ls -la /var/www/html/wp-config.php
```

## Auditing Current Permissions

The commands below find files and directories with permissions more open than recommended. Run them to audit an existing WordPress installation without making changes.

```bash
# Find world-writable files (almost never correct).
sudo find /var/www/html -type f -perm -002

# Find world-writable directories (almost never correct).
sudo find /var/www/html -type d -perm -002

# Find files owned by root that should be owned by the web server.
sudo find /var/www/html -not -user www-data

# Find PHP files in the uploads directory (these should not exist).
sudo find /var/www/html/wp-content/uploads -type f -name "*.php"

# Check wp-config.php specifically.
sudo stat -c '%a %U %G %n' /var/www/html/wp-config.php
```

## Common Permission Anti-Patterns

These are commonly recommended in low-quality WordPress tutorials and should be avoided:

| Anti-pattern | Why It's Wrong |
|---|---|
| `chmod -R 777 wp-content/` | Allows any user on the system to write to plugin and theme files. A foothold on another site on the same server can compromise this one. |
| `chmod 777 wp-config.php` | Exposes database credentials and auth keys to every user on the server. |
| Running WordPress as root | Any code execution becomes root code execution. |
| Setting permissions only once and never auditing | Plugins and updates frequently create files with wrong ownership or permissions. |

## Notes on Shared Hosting

Many shared hosting environments use suEXEC or similar isolation, where PHP runs as the account owner rather than as a generic web server user. On those hosts:

- The owner of WordPress files is typically the hosting account user
- `wp-config.php` at `600` is correct
- The hosting control panel often enforces specific permissions that override manual changes

If your host enforces a specific permission scheme, follow their guidance. The values in this document assume a self-managed VPS or dedicated server.

## After Permission Changes

After applying permission changes, verify:

- [ ] WordPress admin still loads
- [ ] WordPress can install plugin and theme updates (or fails predictably if `DISALLOW_FILE_MODS` is set)
- [ ] Media uploads still work
- [ ] No PHP errors appear in error logs related to file access
- [ ] Cache plugins still write to their cache directories
