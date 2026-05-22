# wsd-wordpress-security

Sanitized WordPress hardening content built from real production WordPress deployments.

This repository focuses on practical configuration templates, hardening checklists, login surface reduction, file integrity references, and operator-safe implementation guidance. It is meant to be useful to defenders running self-hosted or managed WordPress, and readable to engineers or business owners reviewing the work.

## Scope

This repository includes:

- Hardened `wp-config.php` configuration template with every directive commented
- Apache `.htaccess` hardening rules for WordPress
- Nginx server block hardening for WordPress
- Login surface hardening checklist
- File permission and ownership reference
- Plugin and theme audit checklist
- Documentation written around manual placement, validation, rollback, and tuning discipline

This repository does not include:

- Turnkey production deployment automation
- Blind copy-all scripts
- Plugin or theme code
- Live malware samples
- Claims that every example here is universally safe to deploy without tuning
- Hardening configurations for WordPress.com hosted sites (different security model)

## Why this repository exists

A lot of public WordPress security content is either too generic, too tied to one specific stack, or written as content marketing rather than as engineering reference. The goal here is different:

- keep the structure clean
- keep the scope honest
- keep the content reusable
- document real operational tradeoffs
- make validation and rollback first-class parts of the workflow

## Who this is for

This repository is aimed at:

- WordPress site owners running self-hosted installations
- WordPress developers and agencies
- Web hosting engineers
- Security practitioners working on WordPress environments
- Blue team engineers handling WordPress in their scope
- Business owners reviewing the work of Web Stack Defense

## Baseline environment

The content in this repository was shaped against real WordPress deployments running:

- WordPress 6.x core
- PHP 8.1 and PHP 8.2 on FPM
- Apache 2.4 with mod_rewrite, and Nginx 1.24
- MySQL 8.0 and MariaDB 10.6
- Ubuntu 22.04 LTS and Ubuntu 24.04 LTS host environments

For exact version and environment notes, see [TESTED_ON.md](TESTED_ON.md).

## Repository layout

```
configs/
  wordpress/
  apache/
  nginx/

checklists/

scripts/

examples/
```

## Content design

The repository is intentionally split into:

**Configuration templates**
Hardened reference files for the three locations where WordPress security is enforced in practice: `wp-config.php`, the Apache `.htaccess` and virtual host layer, and the Nginx server block. Every directive is commented with the reason it exists.

**Checklists**
Operational checklists for login hardening, file permissions, and plugin or theme audits. These are written to be used during real reviews, not just read once.

**Scripts**
Lightweight diagnostic scripts for verifying file permissions, core file integrity, and configuration drift. Scripts assume nothing about the target environment beyond a standard LAMP or LEMP stack.

## Installation philosophy

This repository assumes manual copy and placement.

That is deliberate.

WordPress hardening content should be reviewed before it is deployed. Configuration changes, `.htaccess` rules, security headers, and login restrictions can break legitimate site functionality if applied without testing. Plugin compatibility, theme expectations, hosting environment behavior, and existing custom code all influence whether a given hardening step is safe in a given environment.

Review the content first. Back up your current configuration. Apply one change at a time. Validate. Roll back quickly if needed.

## Validation workflow

Recommended order:

1. Take a full site backup including database and files
2. Apply one configuration change or hardening step
3. Verify WordPress admin access still works
4. Verify front-end site rendering is unaffected
5. Verify critical site functionality (forms, checkout, login) still works
6. Test with a non-admin user account to catch permission regressions
7. Monitor error logs for 24 hours before applying the next change
8. Document any tuning or compatibility issues before expanding further

Use a staging environment for every change where possible. Production-first hardening is how working sites get broken.

## Risks and guardrails

This repository assumes you understand the following risks:

- Aggressive `.htaccess` rules can block legitimate admin functionality
- Disabling XML-RPC breaks Jetpack, mobile apps, and some legitimate integrations
- Restricting the REST API can break Gutenberg block editor functionality
- File permission tightening can break plugins that expect write access
- Security headers can break embeds, third-party scripts, and analytics
- Login URL changes break bookmarked admin URLs and break some plugins
- Configuration drift across environments creates support burden over time
- Hardening that is not documented internally becomes a future incident

## Redaction policy

Nothing in this repository should expose:

- public or internal IP addresses
- domains tied to private infrastructure
- API keys, salts, or authentication tokens
- usernames or database credentials
- hostnames
- file paths unique to a specific deployment

All examples use the documentation reserved range `203.0.113.0/24` for IP addresses, `example.com` for domain names, and clearly marked placeholder variables for credentials.

## Affiliate disclosure

Some setup notes may reference hosting providers or commercial security tools using affiliate links. If you choose to use one, Web Stack Defense may receive a referral credit at no extra cost to you. Support is appreciated, but use whatever provider or tool fits your environment.

## Related platform

This repository is part of the broader website security work documented at [Web Stack Defense](https://www.webstackdefense.com). Guides on the site go deeper into context, tradeoffs, and implementation decisions. The configurations and checklists here are the practical artifacts that sit alongside those guides.

## Contribution policy

Issues and curated pull requests are allowed, but this is not an open-ended community repo where every submission will be merged.

Security issues should be reported privately according to [SECURITY.md](SECURITY.md).

Contribution standards are documented in [CONTRIBUTING.md](CONTRIBUTING.md).
