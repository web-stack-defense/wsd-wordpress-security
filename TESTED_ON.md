# Tested Environment

The content in this repository was shaped against the following environments.

## WordPress

- WordPress 6.4, 6.5, 6.6 core
- WordPress 6.x with Gutenberg block editor
- WordPress 6.x with Classic Editor plugin

## PHP

- PHP 8.1.x on PHP-FPM
- PHP 8.2.x on PHP-FPM
- Tested against `php.ini` defaults with selective hardening overrides

## Web Servers

- Apache 2.4.x with mod_rewrite, mod_headers, mod_ssl
- Nginx 1.22.x and 1.24.x with stable module set

## Databases

- MySQL 8.0.x
- MariaDB 10.6.x

## Host Operating Systems

- Ubuntu 22.04 LTS
- Ubuntu 24.04 LTS
- Debian 12

## Notes on Compatibility

- Configurations have not been tested against Windows IIS deployments
- Configurations assume standard `wp-content` directory location
- Configurations assume `wp-config.php` is in the WordPress root, not one directory above
- Multisite installations require additional considerations not covered in baseline configs

If your environment differs from the above, review the relevant configuration file comments before applying changes.
