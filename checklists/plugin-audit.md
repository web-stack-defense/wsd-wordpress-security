# WordPress Plugin Audit Checklist

Framework for evaluating WordPress plugins before installation and reviewing existing plugins on a live site.

Plugin vulnerabilities are the single largest source of WordPress compromises. The cost of installing a plugin extends well past the install button — every active plugin is part of the site's attack surface, update burden, and incident response scope.

## Pre-Installation Audit

Use this before adding any new plugin to a site.

### Source and Distribution

- [ ] Plugin is hosted on [wordpress.org/plugins](https://wordpress.org/plugins/) or a known commercial vendor
- [ ] Plugin is NOT downloaded from a third-party blog, forum, or "nulled" plugin site
- [ ] Plugin ZIP signature or checksum matches the official source if available

### Maintenance Status

- [ ] Plugin has been updated within the last 12 months
- [ ] Plugin is marked as compatible with the current WordPress major version
- [ ] Plugin author or organization is still active (check their wordpress.org profile)
- [ ] Plugin has not been removed from the WordPress.org plugin directory and reinstated — this often indicates a past security issue

### Vulnerability History

- [ ] Plugin has no unresolved entries in [Patchstack](https://patchstack.com/database/) or [WPScan](https://wpscan.com/plugins)
- [ ] Past vulnerabilities (if any) were resolved in a reasonable timeframe (under 30 days for critical issues)
- [ ] The plugin author has a documented security disclosure process

### Code Quality Signals

- [ ] Plugin has more than 10 active installations (extremely low install counts may suggest abandonment or fraud)
- [ ] Plugin reviews do not show repeated complaints about security, malware, or compromise
- [ ] Plugin does not request capabilities or permissions that exceed its stated function
- [ ] Plugin does not require disabling other security plugins to work
- [ ] Plugin does not phone home to unexpected domains (review with browser dev tools after install in staging)

### Functional Need

- [ ] The functionality cannot be achieved through:
  - [ ] WordPress core
  - [ ] An existing installed plugin
  - [ ] The site's theme
  - [ ] A small custom code snippet (often safer than a full plugin)
- [ ] The plugin will be actively used, not "installed for later"

### Staging Test

- [ ] Plugin has been installed and activated in a staging environment first
- [ ] Staging tests confirm the plugin does not break existing functionality
- [ ] Staging tests confirm the plugin does not introduce new errors in the error log

## Existing Plugin Audit

Use this quarterly on an active site to review the installed plugin set.

### Inventory

- [ ] Full list of installed plugins is documented (name, version, author, purpose)
- [ ] Plugins marked as "inactive" are removed entirely, not left in place
- [ ] No plugin shows "Update available" status without a known reason

### Update Status

- [ ] All active plugins are running the latest version
- [ ] If a plugin is held back from updating, the reason is documented (for example, known compatibility issue with a specific update)
- [ ] Held-back plugins are revisited at least monthly to see if the issue is resolved

### Active vs Necessary

For each active plugin, confirm:

- [ ] The plugin is still in use
- [ ] The original reason for installing it still applies
- [ ] No core WordPress feature has since replaced its function
- [ ] No other installed plugin duplicates its function

Common candidates for removal:

- Old SEO plugins replaced by newer ones
- Page builders no longer used after a theme change
- Caching plugins replaced by a CDN or server-level cache
- Analytics plugins replaced by tag manager implementations
- Form plugins from previous site iterations
- "Helper" or "utility" plugins installed once and forgotten

### Vulnerability Check

- [ ] Each active plugin checked against [Patchstack](https://patchstack.com/database/) or [WPScan](https://wpscan.com/plugins)
- [ ] Any plugin with an unresolved high or critical CVE is removed or replaced
- [ ] Any plugin abandoned by its author (no updates for 24+ months) is flagged for replacement

### Premium License Status

- [ ] Premium plugin licenses are current and renewable
- [ ] Premium plugins running on expired licenses (and therefore not receiving security updates) are renewed or removed

## Plugin Vulnerability Response

When a vulnerability is disclosed in a plugin you use:

- [ ] Confirm whether the vulnerability applies to your installed version
- [ ] Confirm whether the vulnerability requires authentication, specific configuration, or specific user actions
- [ ] If a patched version is available, update immediately after a backup
- [ ] If no patched version is available, evaluate:
  - [ ] Can the plugin be deactivated temporarily without breaking the site
  - [ ] Can the vulnerable functionality be blocked at the WAF or web server level
  - [ ] Should the plugin be replaced with an alternative
- [ ] After patching, verify the site still functions and the vulnerability is no longer exploitable

## Decommissioning a Plugin

When removing a plugin:

- [ ] Plugin is deactivated first, then deleted (not just deactivated)
- [ ] Deactivation is followed by a check of plugin-created database tables (some plugins leave orphan tables on uninstall)
- [ ] Deactivation is followed by a check of plugin-created files outside the plugin directory (uploads, custom directories)
- [ ] Cron jobs registered by the plugin are removed if they persist after uninstall
- [ ] Any web server rules added specifically for the plugin are removed
- [ ] Documentation is updated to reflect the plugin removal

## Documentation Output

The output of a plugin audit should produce:

- [ ] Updated inventory of active plugins
- [ ] List of plugins removed during the audit
- [ ] List of plugins flagged for monitoring or replacement
- [ ] Date of the next scheduled audit
