#!/usr/bin/env bash
# =============================================================================
# WordPress Permission and Hygiene Audit
#
# Repository: wsd-wordpress-security
# Maintained as part of Web Stack Defense — https://www.webstackdefense.com
#
# Read-only audit script. Does not modify any files.
#
# Scans a WordPress installation for common permission, ownership, and file
# hygiene issues. Reports findings to stdout. Returns exit code 0 if no
# issues found, 1 if issues found.
#
# Usage:
#   ./wp-permission-audit.sh /path/to/wordpress
#
# Example:
#   ./wp-permission-audit.sh /var/www/html
#
# Requirements:
#   - Bash 4.0+
#   - find, stat, awk (standard Linux utilities)
#   - Read access to the WordPress directory
# =============================================================================

set -euo pipefail

# -----------------------------------------------------------------------------
# Argument handling
# -----------------------------------------------------------------------------

if [[ $# -ne 1 ]]; then
    echo "Usage: $0 /path/to/wordpress" >&2
    exit 2
fi

WP_PATH="$1"

if [[ ! -d "$WP_PATH" ]]; then
    echo "Error: $WP_PATH is not a directory" >&2
    exit 2
fi

if [[ ! -f "$WP_PATH/wp-config.php" ]] && [[ ! -f "$WP_PATH/wp-config-sample.php" ]]; then
    echo "Warning: $WP_PATH does not appear to be a WordPress installation" >&2
    echo "         (no wp-config.php or wp-config-sample.php found)" >&2
fi

# -----------------------------------------------------------------------------
# Counters
# -----------------------------------------------------------------------------

ISSUES_FOUND=0

# -----------------------------------------------------------------------------
# Reporting helpers
# -----------------------------------------------------------------------------

report_section() {
    echo ""
    echo "=============================================================="
    echo "  $1"
    echo "=============================================================="
}

report_issue() {
    echo "  [ISSUE] $1"
    ISSUES_FOUND=$((ISSUES_FOUND + 1))
}

report_ok() {
    echo "  [OK]    $1"
}

# -----------------------------------------------------------------------------
# Check 1: wp-config.php permissions
# -----------------------------------------------------------------------------

report_section "wp-config.php permissions"

if [[ -f "$WP_PATH/wp-config.php" ]]; then
    PERMS=$(stat -c '%a' "$WP_PATH/wp-config.php")
    if [[ "$PERMS" == "600" ]] || [[ "$PERMS" == "640" ]]; then
        report_ok "wp-config.php permissions are $PERMS (acceptable)"
    elif [[ "$PERMS" == "644" ]]; then
        report_issue "wp-config.php permissions are 644 — consider tightening to 640 or 600"
    else
        report_issue "wp-config.php permissions are $PERMS — should be 600 or 640"
    fi
else
    report_issue "wp-config.php not found at $WP_PATH"
fi

# -----------------------------------------------------------------------------
# Check 2: World-writable files
# -----------------------------------------------------------------------------

report_section "World-writable files"

WORLD_WRITABLE=$(find "$WP_PATH" -type f -perm -002 2>/dev/null | head -20)

if [[ -z "$WORLD_WRITABLE" ]]; then
    report_ok "No world-writable files found"
else
    report_issue "World-writable files found (showing up to 20):"
    echo "$WORLD_WRITABLE" | sed 's/^/    /'
fi

# -----------------------------------------------------------------------------
# Check 3: World-writable directories
# -----------------------------------------------------------------------------

report_section "World-writable directories"

WORLD_WRITABLE_DIRS=$(find "$WP_PATH" -type d -perm -002 2>/dev/null | head -20)

if [[ -z "$WORLD_WRITABLE_DIRS" ]]; then
    report_ok "No world-writable directories found"
else
    report_issue "World-writable directories found (showing up to 20):"
    echo "$WORLD_WRITABLE_DIRS" | sed 's/^/    /'
fi

# -----------------------------------------------------------------------------
# Check 4: PHP files in uploads directory
# -----------------------------------------------------------------------------

report_section "PHP files in wp-content/uploads"

if [[ -d "$WP_PATH/wp-content/uploads" ]]; then
    PHP_IN_UPLOADS=$(find "$WP_PATH/wp-content/uploads" -type f \( -name "*.php" -o -name "*.phtml" -o -name "*.phar" \) 2>/dev/null | head -20)

    if [[ -z "$PHP_IN_UPLOADS" ]]; then
        report_ok "No PHP files found in uploads directory"
    else
        report_issue "PHP files found in uploads directory (potential webshells):"
        echo "$PHP_IN_UPLOADS" | sed 's/^/    /'
    fi
else
    report_ok "uploads directory does not exist (nothing to check)"
fi

# -----------------------------------------------------------------------------
# Check 5: Sensitive files that should not be web-accessible
# -----------------------------------------------------------------------------

report_section "Sensitive files in web root"

for sensitive in "readme.html" "license.txt" "wp-config-sample.php" "debug.log"; do
    if [[ -f "$WP_PATH/$sensitive" ]]; then
        report_issue "$sensitive present in web root (information disclosure)"
    else
        report_ok "$sensitive not present"
    fi
done

# -----------------------------------------------------------------------------
# Check 6: Common deployment artifacts
# -----------------------------------------------------------------------------

report_section "Deployment artifacts in web root"

for artifact in ".git" ".svn" ".hg" ".env" "composer.json" "package.json"; do
    if [[ -e "$WP_PATH/$artifact" ]]; then
        report_issue "$artifact present in web root (should not be web-accessible)"
    else
        report_ok "$artifact not present"
    fi
done

# -----------------------------------------------------------------------------
# Check 7: Ownership consistency
# -----------------------------------------------------------------------------

report_section "Ownership consistency"

OWNERS=$(find "$WP_PATH" -maxdepth 2 -printf '%u\n' 2>/dev/null | sort -u | wc -l)

if [[ "$OWNERS" -eq 1 ]]; then
    OWNER=$(find "$WP_PATH" -maxdepth 0 -printf '%u' 2>/dev/null)
    report_ok "All files in top two levels owned by $OWNER"
else
    report_issue "Mixed ownership detected in top two levels ($OWNERS distinct owners) — review for consistency"
fi

# -----------------------------------------------------------------------------
# Summary
# -----------------------------------------------------------------------------

report_section "Summary"

if [[ "$ISSUES_FOUND" -eq 0 ]]; then
    echo "  No issues found."
    exit 0
else
    echo "  $ISSUES_FOUND issue(s) found. Review above for details."
    exit 1
fi
