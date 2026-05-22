# WordPress Login Hardening Checklist

Login surface hardening for WordPress. Use this checklist as part of an initial site review or as a quarterly audit.

The login surface is where most automated WordPress attacks land. It includes `/wp-login.php`, `/wp-admin/`, `/xmlrpc.php`, and the REST API authentication endpoints.

## Pre-flight

- [ ] Full site backup taken (files and database) before starting
- [ ] Staging environment available for testing changes
- [ ] At least two administrator accounts exist (one to test with, one as a fallback)
- [ ] Admin email address on file is current and accessible

## Account Hygiene

- [ ] No user has the username `admin`, `administrator`, `root`, or the site name
- [ ] All administrator accounts have a unique email address
- [ ] All administrator accounts use a password manager (no shared or reused passwords)
- [ ] Inactive administrator accounts are removed or downgraded to a lower role
- [ ] Stale subscriber accounts created by spam registrations are removed
- [ ] User registration is disabled if the site does not need public registration (Settings → General)
- [ ] Default new user role is set to Subscriber, not Editor or higher

## Password Policy

- [ ] Passwords for all administrator accounts have been rotated within the last 12 months
- [ ] No administrator account uses a password from a known breach (check via [haveibeenpwned.com](https://haveibeenpwned.com/Passwords))
- [ ] Password reset emails go to a monitored mailbox, not a forwarding alias that could be hijacked

## Two-Factor Authentication

- [ ] Two-factor authentication is enabled for every administrator account
- [ ] Backup codes for 2FA are stored in a password manager, not in email
- [ ] 2FA recovery method does not rely on SMS where possible (use TOTP apps or hardware keys)
- [ ] A documented recovery procedure exists in case the primary 2FA device is lost

## Login URL Hardening

- [ ] `/wp-admin/` and `/wp-login.php` are not exposed to the entire internet if a smaller admin IP set is feasible
- [ ] If IP allowlisting is used, the allowlist is documented and reviewed quarterly
- [ ] Custom login URL plugin (if used) does not leak the real URL through error pages, REST API, or sitemaps

## Brute Force Protection

- [ ] A login rate limiter is in place at one of the following layers:
  - [ ] Server level (Nginx `limit_req`, Apache `mod_evasive`)
  - [ ] CDN or WAF level (Cloudflare rate limiting rule)
  - [ ] Plugin level (Wordfence, Limit Login Attempts Reloaded, Solid Security)
- [ ] Failed login attempts trigger temporary lockout after a defined threshold
- [ ] Lockouts are logged and reviewable
- [ ] Lockout duration is long enough to be operationally meaningful (15+ minutes minimum)

## XML-RPC

- [ ] XML-RPC usage has been confirmed or denied for the site:
  - [ ] Jetpack is in use → keep XML-RPC enabled, but block `system.multicall` at WAF level
  - [ ] WordPress mobile apps are in use → keep XML-RPC enabled
  - [ ] Pingbacks and trackbacks are required → keep XML-RPC enabled
  - [ ] None of the above → disable XML-RPC at the web server level
- [ ] If XML-RPC is enabled, brute force attempts against it are monitored or rate limited
- [ ] The `system.multicall` method is blocked or restricted to known IPs if XML-RPC is needed

## REST API Authentication

- [ ] REST API user enumeration endpoint (`/wp-json/wp/v2/users`) is restricted or rate limited
- [ ] Application Passwords are reviewed quarterly — revoke any that are not actively in use
- [ ] Application Password creation is logged and monitored

## User Enumeration

- [ ] Direct username enumeration is blocked at the server level (see `.htaccess` and Nginx configurations in this repository)
- [ ] WordPress error messages on the login page do not distinguish "invalid username" from "invalid password" (default behavior in modern WordPress is already correct, but plugins can break this)
- [ ] Author archive pages do not expose usernames if author archives are not a public feature of the site

## Session Management

- [ ] Active session list for administrator accounts is reviewable
- [ ] Documented procedure exists to invalidate all sessions during incident response (rotating `AUTH_KEY` salts in `wp-config.php`)
- [ ] Session cookies are set with `Secure` and `HttpOnly` flags (default in modern WordPress over HTTPS)

## Monitoring and Alerting

- [ ] Failed login attempts are logged
- [ ] Successful logins from new IPs or geographies trigger an alert
- [ ] Administrator account creation triggers an alert
- [ ] Role escalation events trigger an alert
- [ ] Login activity is reviewed at least monthly

## Documentation

- [ ] Recovery procedure for locked-out administrators is documented
- [ ] List of authorized administrator accounts is documented and current
- [ ] The above documentation is stored somewhere accessible during an incident, not only on the site itself

## Validation

After making changes from this checklist:

- [ ] Confirm you can still log in as an administrator
- [ ] Confirm you can still log in as a non-administrator user
- [ ] Confirm password reset emails arrive
- [ ] Confirm 2FA recovery flow works for at least one account
- [ ] Confirm front-end site rendering is unaffected
